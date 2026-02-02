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

  var btn = document.getElementById('generateBtn');
  var result = document.getElementById('result');
  var firstNameResult = document.getElementById('firstNameResult');
  var lastNameResult = document.getElementById('lastNameResult');
  var emailResult = document.getElementById('emailResult');
  var zipResult = document.getElementById('zipResult');
  var copySsnBtn = document.getElementById('copySsnBtn');
  var copyFirstNameBtn = document.getElementById('copyFirstNameBtn');
  var copyLastNameBtn = document.getElementById('copyLastNameBtn');
  var copyEmailBtn = document.getElementById('copyEmailBtn');
  var copyZipBtn = document.getElementById('copyZipBtn');

  btn.addEventListener('click', function () {
    result.textContent = generateSSN();
    var first = generateFirstName();
    var last = generateLastName();
    firstNameResult.textContent = first;
    lastNameResult.textContent = last;
    emailResult.textContent = generateEmail(first, last);
    zipResult.textContent = generateZipCode();
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
})();
