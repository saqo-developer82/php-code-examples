<?php

namespace App\ImportEvents;

use Event;
use Country;
use DOMXPath;
use DOMDocument;
use QueryBuilder;
use Api\Form\Event as EventForm;

class ImportEventsTarget1
{
    private $event = [
        'user_id' => 0,
        'event_source_id' => '',
        'title' => '',
        'organisation_id' => '',
        'slug' => '',
        'tagline' => '',
        'description' => '',
        'logo' => '',
        'policy_id' => '',
        'event_type_id' => '',
        'city' => '',
        'country_code' => '',
        'language' => 'en',
        'website' => '',
        'status' => 'draft',
        'start_date' => '',
        'end_date' => '',
        'timezone' => '',
        'thumb' => '1',
        'created' => '',
        'published' => '',
        'views' => '',
        'shared_fb' => '',
        'shared_tw' => '',
    ];
    private $host = 'https://research-and-innovation.ec.europa.eu';
    private $pathName = '/events/upcoming-events_en';
    private $countriesCheck;
    private $countries;
    private $event_source;
    private $parsed_events_count = 0;
    private $saved_events_count = 0;

    public function mainAction()
    {
        $url = $this->host . $this->pathName;
        $event_sources_model = new QueryBuilder('EventSource');
        $this->event_source = $event_sources_model->where('events_website', $url)->first();
        $this->countries = (new Country())->find()->toArray();
        $this->countriesCheck = getCountriesWithCity()->data;

        $this->getPageContent($url, 0);

        return ['parsedEvents' => $this->parsed_events_count, 'savedEvents' => $this->saved_events_count];
    }

    private function getPageContent($url, $index)
    {
        list ($finder, $dom) = $this->domFinder($url . '?page=' . $index);

        $result = $finder->query('//div[contains(@class, "ecl-content-item-block")]//div[contains(@class, "ecl-content-item-block__item contextual-region ecl-u-mb-l ecl-col-12")]');

        $this->parsed_events_count += count($result);

        if (count($result)) {
            $this->getSinglePageData($result, $finder);
            $this->getPageContent($url, $index + 1);
        }
    }

    public function findTitle($finder, $item) {
        $titleSlug = $finder->query('.//div[@class="ecl-content-block__title"]//a', $item);
        $title = '';
        $slug = '';

        if ($titleSlug->length > 0) {
            $title = trim($titleSlug->item(0)->nodeValue);
            $slug = slugify($title);
        }

        return [$title, $slug];
    }

    public function findWebsite($finder, $item) {
        $link = $finder->query('.//div[@class="ecl-content-block__title"]//a', $item);
        $website = '';

        if ($link->length) {
            $website = $this->host . $link->item(0)->getAttribute('href');

            if (strpos(strtolower($link->item(0)->getAttribute('href')), 'http') !== false) {
                $website = $link->item(0)->getAttribute('href');
            }
        }

        return $website;
    }

    public function findEventType($finder, $item) {
        $location = $finder->query('.//div[@class="ecl-content-block__description"]', $item);
        $event_type_id = 3;
        $timezone = '';

        if ((strpos(strtolower($location->item(0)->nodeValue), 'online') !== false) ||
            (strpos(strtolower($location->item(0)->nodeValue), 'live') !== false)) {
            $event_type_id = 2;
            $timezone = 'GMT +2:00';
        }

        return [$event_type_id, $timezone];
    }

    public function findLocation($finder, $item) {
        $location = $finder->query('.//div[@class="ecl-content-block__description"]//li[@class="ecl-u-d-flex ecl-u-align-items-center ecl-unordered-list__item ecl-u-mh-none ecl-u-ph-none"]', $item);
        $city = '';
        $country = '';

        if ($location->length > 0) {
            $location = explode(', ', trim($location->item(0)->nodeValue));

            if (count($location) > 1) {
                $city = $location[0];
                $country = $location[1];

                if (!empty($country)) {
                    $secondCheckCity = $this->getCountryByCity($country);

                    if (!empty($secondCheckCity)) {
                        $country = $secondCheckCity;
                        $city = $location[1];
                    }
                }
            } else if (count($location) == 1) {
                $country = $this->getCountryByCity($location[0]);

                if (!empty($country)) {
                    $city = $location[0];
                } else {
                    $country = $location[0];
                }
            }
        }

        if (!empty($country)) {
            $country = findCountryCode($country, $this->countries);
        }

        return [$city, $country];
    }

    public function findSingleLocation($finder) {
        $location = $finder->query('//dl[@class="ecl-description-list ecl-description-list--horizontal ecl-u-mb-s ecl-u-mt-s"]//li[@class="ecl-description-list__definition-item"]');
        $city = '';
        $country = '';

        if ($location->length > 0) {
            $country = $location->item(0)->nodeValue;
        }

        if (!empty($country)) {
            $country = findCountryCode($country, $this->countries);
        }

        return [$city, $country];
    }

    public function findDateTimes($finder, $item) {
        $dateTime = $finder->query('.//time[@class="ecl-date-block ecl-date-block--ongoing ecl-content-item__date"]', $item);
        $start_date = '';
        $end_date = '';

        if ($dateTime->length) {
            list ($start_date, $end_date) = $this->getDateTime(trim($dateTime->item(0)->nodeValue));
        }

        return [$start_date, $end_date];
    }

    public function findTagline($finder) {
        $taglineDescription = $finder->query('//div[@class="ecl-page-header__description-container"]');
        $tagline = '';

        if ($taglineDescription->length) {
            $tagline = trim($taglineDescription->item(0)->nodeValue);
        }

        return $tagline;
    }

    public function findDescription($finder, $dom) {
        $description1 = $finder->query('//div[@id="event-details"]//div[@class="ecl-col-12 ecl-col-m-6"]//div[@class="ecl"]//div[@class="ecl"]');
        $description2 = $finder->query('//div[@class="ecl-featured-item__description"]');
        $description3 = $finder->query('(//div[@class="elementor-widget-container"])[12]');
        $description4 = $finder->query('//div[@id="the-conference"]//div[contains(@class, "et_pb_module et_pb_text")][2]');
        $descriptionFinder = '';

        $mergedDescriptions = array_merge(
                                iterator_to_array($description1),
                                iterator_to_array($description2),
                                iterator_to_array($description3),
                                iterator_to_array($description4)
                            );

        if (!empty($mergedDescriptions)) {
            foreach ($mergedDescriptions as $desc) {
                $htmlWithTags = $dom->saveHTML($desc);

                $doc = new DOMDocument();
                @$doc->loadHTML(mb_convert_encoding($htmlWithTags, 'HTML-ENTITIES', 'UTF-8'));

                $elements = $doc->getElementsByTagName('*');

                foreach ($elements as $element) {
                    $attributes = $element->attributes;

                    foreach ($attributes as $attrName => $attrNode) {
                        $element->removeAttribute($attrName);
                    }

                    foreach ($element->getElementsByTagName('style') as $elem) {
                        $elem->parentNode->removeChild($elem);
                    }
                }

                $htmlWithoutAttributes = $doc->saveHTML();
                $htmlWithTags = strip_tags($htmlWithoutAttributes, '<p><ul><li><br>');

                if (!empty($htmlWithTags)) {
                    $htmlWithTags = preg_replace('/<strong>|<\/strong>|<b>|<\/b>/', '', $htmlWithTags);
                    $descriptionFinder .= '<p>' . trim($htmlWithTags) . '</p>';
                }
            }
        }

        return $descriptionFinder;
    }

    public function findLogo($finder) {
        $logoItem = $finder->query('//img[@class="ecl-media-container__media"]');
        $logo = '';

        if ($logoItem->length) {
            $logo = $logoItem->item(0)->getAttribute('src');
        }

        return $logo;
    }

    private function getSinglePageData($result, $finder)
{
    foreach ($result as $key => $item) {
        list ($this->event['title'], $this->event['slug']) = $this->findTitle($finder, $item);
        list ($this->event['event_type_id'], $this->event['timezone']) = $this->findEventType($finder, $item);
        list ($this->event['city'], $this->event['country_code']) = $this->findLocation($finder, $item);
        $this->event['website'] = $this->findWebsite($finder, $item);
        list ($this->event['start_date'], $this->event['end_date']) = $this->findDateTimes($finder, $item);

        list ($finderSingle, $dom) = $this->domFinder($this->event['website']);
        $this->event['tagline'] = $this->findTagline($finderSingle);
        $this->event['description'] = $this->findDescription($finderSingle, $dom);
        $this->event['logo'] = $this->findLogo($finderSingle);
        $this->event['event_source_id'] = $this->event_source->id;
        $this->event['policy_id'] = $this->event_source->policy_id;
        $this->event['organisation_id'] = $this->event_source->organisation_id ?? '';
        $this->event['created'] = $this->event['published'] = date('Y-m-d H:i:s');

        // Default timezone if missing
        if (empty($this->event['timezone'])) {
            $this->event['timezone'] = 'Europe/Brussels';
            echo "⚠️  Defaulted timezone to Europe/Brussels\n";
        }

        // Debug output before validation
        echo "\n--------------------\n";
        echo "Title:        {$this->event['title']}\n";
        echo "Website:      {$this->event['website']}\n";
        echo "Start date:   {$this->event['start_date']}\n";
        echo "End date:     {$this->event['end_date']}\n";
        echo "City:         {$this->event['city']}\n";
        echo "Country code: {$this->event['country_code']}\n";
        echo "Timezone:     {$this->event['timezone']}\n";
        echo "Description:  " . (strlen($this->event['description']) > 0 ? '✔' : '✘') . "\n";

        if (!$this->validation($this->event)) {
            echo "⛔ Skipped: Validation failed.\n";
            continue;
        }

        try {
            $this->saveAction($this->event);
            echo "✅ Saved\n";
        } catch (\PDOException|\Exception $e) {
            echo "❌ Save error: " . $e->getMessage() . "\n";
        }
    }
}

    public function saveAction($event) {
        $entity = new Event();
        $form = new EventForm();

        $form->bind($event, $entity);
        $entity->save();
        $this->saved_events_count++;
    }

    public function validation($event) {
        $model = new QueryBuilder('Event');
        $exist = $model->where('slug', $event['slug'])->first();
        $validation = true;

        if (!empty($exist) ||
            empty($event['end_date']) ||
            (strtotime($event['end_date']) < time()) ||
            empty($event['description']) ||
            (empty($event['timezone']) && empty($event['country_code']))) {
            $validation = false;
        }

        return $validation;
    }

    public function getCountryByCity($city) {
        $country = '';

        if (strtolower($city) == 'brussels') {
            $city = 'brussel';
        }

        if (strtolower($city) == 'lisbon') {
            $city = 'lisboa';
        }

        if (!empty($city)) {
            foreach ($this->countriesCheck as $countryItem) {
                if (strpos(strtolower($countryItem->city), strtolower($city)) !== false) {
                    $country = $countryItem->country;
                }
            }
        }

        return $country;
    }

    public function getDateTime($dateTime) {
        $startDateTime = '';
        $endDateTime = '';

        $dateTime = explode('-', $dateTime);

        if (count($dateTime) == 1) {
            $startDateTime = date('Y-m-d', strtotime($dateTime[0])) . ' 00:00:00';
            $endDateTime = date('Y-m-d', strtotime($dateTime[0])) . ' 23:59:59';

        } else if (count($dateTime) > 1) {
            $startDateTime = date('Y-m', strtotime($dateTime[1])) . '-' . $dateTime[0] . ' 00:00:00';
            $endDateTime = date('Y-m-d', strtotime($dateTime[1])) . ' 23:59:59';
        }

        return [$startDateTime, $endDateTime];
    }

    private function domFinder($url) {
        $dom = new DOMDocument("1.0", "utf-8");
        $html = fetchUrl(
            $url,
            'GET',
            $this->event_source->proxy_host,
            $this->event_source->proxy_username,
            $this->event_source->proxy_password
        );

        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $finder = new DOMXPath($dom);

        return [$finder, $dom];
    }
}
