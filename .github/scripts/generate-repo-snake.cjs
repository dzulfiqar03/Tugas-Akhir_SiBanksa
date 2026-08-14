const { execSync } = require('child_process');
const fs = require('fs');

// 1. Ambil semua tanggal commit di repo ini (branch aktif, seluruh history)
const log = execSync('git log --pretty=format:%ad --date=short').toString().trim();
const dates = log.split('\n').filter(Boolean);

const counts = {};
for (const d of dates) counts[d] = (counts[d] || 0) + 1;

// 2. Bangun grid 53 minggu x 7 hari, berakhir hari ini
const WEEKS = 53;
const today = new Date();
today.setHours(0, 0, 0, 0);
const endSunday = new Date(today);
endSunday.setDate(endSunday.getDate() - endSunday.getDay());
const startDate = new Date(endSunday);
startDate.setDate(startDate.getDate() - (WEEKS - 1) * 7);

const cells = [];
for (let w = 0; w < WEEKS; w++) {
  for (let d = 0; d < 7; d++) {
    const date = new Date(startDate);
    date.setDate(date.getDate() + w * 7 + d);
    if (date > today) continue;
    const key = date.toISOString().slice(0, 10);
    cells.push({ w, d, date: key, count: counts[key] || 0 });
  }
}

// 3. Warna berdasarkan intensitas commit (tema biru, senada README)
const max = Math.max(1, ...cells.map((c) => c.count));
function levelColor(count) {
  if (count === 0) return '#1b2733';
  const ratio = count / max;
  if (ratio > 0.75) return '#0e75b6';
  if (ratio > 0.5) return '#2f8fd1';
  if (ratio > 0.25) return '#5fabe0';
  return '#8fc6ec';
}

// 4. Layout grid
const CELL = 12, GAP = 3, PAD = 20;
const cellX = (w) => PAD + w * (CELL + GAP);
const cellY = (d) => PAD + d * (CELL + GAP);
const width = PAD * 2 + WEEKS * (CELL + GAP);
const height = PAD * 2 + 7 * (CELL + GAP);

// 5. Jalur ular: urut kolom demi kolom (minggu), lalu baris (hari)
const ordered = cells.slice().sort((a, b) => a.w - b.w || a.d - b.d);
const pathD = 'M ' + ordered.map((c) => `${cellX(c.w) + CELL / 2},${cellY(c.d) + CELL / 2}`).join(' L ');

const DURATION = 24; // detik untuk satu putaran penuh
const total = ordered.length;

// 6. Kotak grid + animasi "nyala" saat dilewati ular
let rects = '';
ordered.forEach((c, i) => {
  const t = (i / total) * DURATION;
  const base = levelColor(c.count);
  rects += `<rect x="${cellX(c.w)}" y="${cellY(c.d)}" width="${CELL}" height="${CELL}" rx="2" fill="${base}">
    <animate attributeName="fill" values="${base};#ffe37a;${base}" keyTimes="0;0.5;1" dur="0.6s" begin="${t}s;${t + DURATION}s" fill="freeze" />
  </rect>\n`;
});

// 7. Badan ular (beberapa segmen mengikuti jalur dengan jeda)
const SEGMENTS = 5;
let snake = '';
for (let i = 0; i < SEGMENTS; i++) {
  const begin = -(i * (DURATION / total) * 1.5);
  const opacity = (1 - i * 0.15).toFixed(2);
  snake += `<circle r="5" fill="#ffe37a" opacity="${opacity}">
    <animateMotion dur="${DURATION}s" repeatCount="indefinite" begin="${begin}s" rotate="auto">
      <mpath href="#snakePath"/>
    </animateMotion>
  </circle>\n`;
}

const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${width} ${height}" width="${width}" height="${height}">
<rect width="100%" height="100%" fill="transparent"/>
<path id="snakePath" d="${pathD}" fill="none" stroke="none"/>
${rects}
${snake}
</svg>`;

fs.mkdirSync('dist', { recursive: true });
fs.writeFileSync('dist/repo-snake.svg', svg);
console.log(`Generated snake: ${total} cells, max commits/hari: ${max}`);
