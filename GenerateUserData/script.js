(function () {
  function randomArea() {
    var r = Math.floor(Math.random() * 898) + 1;
    return r >= 666 ? r + 1 : r;
  }

  function randomGroup() {
    return Math.floor(Math.random() * 99) + 1;
  }

  function randomSerial() {
    return Math.floor(Math.random() * 9999) + 1;
  }

  function pad(n, len) {
    return String(n).padStart(len, '0');
  }

  function generateSSN() {
    var area = pad(randomArea(), 3);
    var group = pad(randomGroup(), 2);
    var serial = pad(randomSerial(), 4);
    return area + '-' + group + '-' + serial;
  }

  var firstNames = [
    'James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda',
    'William', 'Elizabeth', 'David', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica',
    'Thomas', 'Sarah', 'Charles', 'Karen', 'Christopher', 'Lisa', 'Daniel', 'Nancy',
    'Matthew', 'Betty', 'Anthony', 'Margaret', 'Mark', 'Sandra', 'Donald', 'Ashley'
  ];
  var lastNames = [
    'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
    'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson',
    'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson',
    'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker'
  ];

  function pick(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
  }

  function generateFirstName() {
    return pick(firstNames);
  }
  function generateLastName() {
    return pick(lastNames);
  }

  function generateEmail(firstName, lastName) {
    var local = (firstName + '.' + lastName).toLowerCase().replace(/\s+/g, '');
    return local + '@example.com';
  }

  function generateZipCode() {
    var part1 = pad(Math.floor(Math.random() * 100000), 4);
    var part2 = pad(Math.floor(Math.random() * 10000), 4);
    return part1 + '-' + part2;
  }

  // Phone: E.164 format, valid patterns for intl-tel-input / libphonenumber
  var phoneCountries = [
    { dial: '1', gen: function () { return randRange(2, 9) + pad(rand(100), 2) + randRange(2, 9) + pad(rand(100), 2) + pad(rand(10000), 4); } },           // US/CA NANP: NPA 2-9xx, NXX 2-9xx, 4 digits
    { dial: '44', gen: function () { return '7' + pad(rand(1000000000), 9); } },                                                                      // UK mobile: 7 + 9 digits
    { dial: '49', gen: function () { var p = ['15', '16', '17'][rand(3)]; return p + pad(rand(100000000), 8); } },                                       // DE mobile: 15x/16x/17x + 8 digits
    { dial: '33', gen: function () { return (rand(2) === 0 ? '6' : '7') + pad(rand(100000000), 8); } },                                                 // FR mobile: 6 or 7 + 8 digits
    { dial: '34', gen: function () { return (rand(4) === 0 ? '6' : rand(4) === 1 ? '7' : rand(4) === 2 ? '8' : '9') + pad(rand(100000000), 8); } },     // ES: 6/7/8/9 + 8 digits
    { dial: '39', gen: function () { return '3' + pad(rand(1000000000), 9); } },                                                                       // IT mobile: 3 + 9 digits
    { dial: '61', gen: function () { return '4' + pad(rand(100000000), 8); } },                                                                       // AU mobile: 4 + 8 digits
    { dial: '81', gen: function () { return (rand(2) === 0 ? '90' : '80') + pad(rand(10000000), 7); } },                                               // JP mobile: 090/080 + 7 digits
    { dial: '86', gen: function () { return '1' + randRange(3, 9) + pad(rand(100000000), 8); } },                                                      // CN mobile: 13x-19x + 8 digits
  ];
  function rand(n) { return Math.floor(Math.random() * n); }
  function randRange(lo, hi) { return lo + Math.floor(Math.random() * (hi - lo + 1)); }
  function generatePhone() {
    var country = phoneCountries[rand(phoneCountries.length)];
    return '+' + country.dial + country.gen();
  }

  function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text);
    } else {
      var ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
    }
  }

  function generateCompanyName(first, last) {
    return first + last + ' Inc.';
  }

  var btn = document.getElementById('generateBtn');
  var result = document.getElementById('result');
  var firstNameResult = document.getElementById('firstNameResult');
  var lastNameResult = document.getElementById('lastNameResult');
  var emailResult = document.getElementById('emailResult');
  var zipResult = document.getElementById('zipResult');
  var phoneResult = document.getElementById('phoneResult');
  var companyNameResult = document.getElementById('companyNameResult');

  var copySsnBtn = document.getElementById('copySsnBtn');
  var copyFirstNameBtn = document.getElementById('copyFirstNameBtn');
  var copyLastNameBtn = document.getElementById('copyLastNameBtn');
  var copyEmailBtn = document.getElementById('copyEmailBtn');
  var copyZipBtn = document.getElementById('copyZipBtn');
  var copyPhoneBtn = document.getElementById('copyPhoneBtn');
  var copyCompanyNameBtn = document.getElementById('copyCompanyNameBtn');

  btn.addEventListener('click', function () {
    result.textContent = generateSSN();
    var first = generateFirstName();
    var last = generateLastName();
    firstNameResult.textContent = first;
    lastNameResult.textContent = last;
    emailResult.textContent = generateEmail(first, last);
    zipResult.textContent = generateZipCode();
    phoneResult.textContent = generatePhone();
    companyNameResult.textContent = generateCompanyName(first, last);
  });

  copySsnBtn.addEventListener('click', function () {
    var text = result.textContent;
    if (text) copyToClipboard(text);
  });
  copyFirstNameBtn.addEventListener('click', function () {
    var text = firstNameResult.textContent;
    if (text) copyToClipboard(text);
  });
  copyLastNameBtn.addEventListener('click', function () {
    var text = lastNameResult.textContent;
    if (text) copyToClipboard(text);
  });
  copyEmailBtn.addEventListener('click', function () {
    var text = emailResult.textContent;
    if (text) copyToClipboard(text);
  });
  copyZipBtn.addEventListener('click', function () {
    var text = zipResult.textContent;
    if (text) copyToClipboard(text);
  });
  copyPhoneBtn.addEventListener('click', function () {
    var text = phoneResult.textContent;
    if (text) copyToClipboard(text);
  });
  copyCompanyNameBtn.addEventListener('click', function () {
    var text = companyNameResult.textContent;
    if (text) copyToClipboard(text);
  });
})();
