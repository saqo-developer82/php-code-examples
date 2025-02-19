<?php

require 'vendor/autoload.php';

$faker = Faker\Factory::create();

// Generate a random first name and last name
$firstName = $faker->firstName;
$lastName = $faker->lastName;

// Output the result
echo "Random First Name: " . $firstName . "\n";
echo "Random Last Name: " . $lastName . "\n";
echo "Email: " . strtolower($firstName) . "." . strtolower($lastName) . "@example.com\n";
