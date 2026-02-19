(function () {
  function pad(n, len) {
    return String(n).padStart(len, '0');
  }
  function randomArea() {
    var r = Math.floor(Math.random() * 898) + 1;
    return r >= 666 ? r + 1 : r;
  }
  function generateSSN() {
    return pad(randomArea(), 3) + '-' + pad(Math.floor(Math.random() * 99) + 1, 2) + '-' + pad(Math.floor(Math.random() * 9999) + 1, 4);
  }
  function generateZipCode() {
    return pad(Math.floor(Math.random() * 10000), 5) + '-' + pad(Math.floor(Math.random() * 10000), 4);
  }
  var phoneCountries = [
    { dial: '1', gen: function () { return (Math.floor(Math.random() * 8) + 2) + pad(Math.floor(Math.random() * 100), 2) + (Math.floor(Math.random() * 8) + 2) + pad(Math.floor(Math.random() * 100), 2) + pad(Math.floor(Math.random() * 10000), 4); } },
    { dial: '44', gen: function () { return '7' + pad(Math.floor(Math.random() * 1000000000), 9); } },
    { dial: '49', gen: function () { return ['15', '16', '17'][Math.floor(Math.random() * 3)] + pad(Math.floor(Math.random() * 100000000), 8); } },
  ];
  function generatePhone() {
    var c = phoneCountries[Math.floor(Math.random() * phoneCountries.length)];
    return '+' + c.dial + c.gen();
  }
  function generateCompanyName(first, last) {
    return (first + last).replace(/\s+/g, '') + ' Inc.';
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
    var row = MOCK_DATA[Math.floor(Math.random() * MOCK_DATA.length)];
    var first = row.first_name;
    var last = row.last_name;
    result.textContent = generateSSN();
    firstNameResult.textContent = first;
    lastNameResult.textContent = last;
    emailResult.textContent = row.email;
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
