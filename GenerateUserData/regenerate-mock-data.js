#!/usr/bin/env node
/**
 * Regenerates _mock_data.js from MOCK_DATA.csv
 * Run: node regenerate-mock-data.js
 */

const fs = require('fs');
const path = require('path');

const dir = __dirname;
const csvPath = path.join(dir, 'MOCK_DATA.csv');
const outPath = path.join(dir, '_mock_data.js');

if (!fs.existsSync(csvPath)) {
  console.error('MOCK_DATA.csv not found');
  process.exit(1);
}

const csv = fs.readFileSync(csvPath, 'utf8');
const lines = csv.trim().split('\n').slice(1);
const rows = lines
  .map((line) => {
    const m = line.match(/^([^,]*),([^,]*),(.*)$/);
    if (!m) return null;
    return {
      first_name: m[1].trim(),
      last_name: m[2].trim(),
      email: m[3].trim(),
    };
  })
  .filter(Boolean);

fs.writeFileSync(outPath, 'var MOCK_DATA=' + JSON.stringify(rows) + ';\n');
console.log(`Regenerated _mock_data.js with ${rows.length} rows`);
