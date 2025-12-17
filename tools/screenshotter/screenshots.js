const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const BASE = process.env.BASE_URL || 'http://localhost/e-bhm_connect/';
const pages = [
  { name: 'home', url: BASE },
  { name: 'about', url: BASE + '?page=about' },
  { name: 'privacy', url: BASE + '?page=privacy' },
  { name: 'help', url: BASE + '?page=help' },
  { name: 'login', url: BASE + '?page=login-bhw' },
  { name: 'reports', url: BASE + '?page=admin-reports' }
];

(async () => {
  const screenshotsDir = path.resolve(__dirname, '../screenshots');
  const lightDir = path.join(screenshotsDir, 'light');
  const darkDir = path.join(screenshotsDir, 'dark');

  if (!fs.existsSync(screenshotsDir)) fs.mkdirSync(screenshotsDir);
  if (!fs.existsSync(lightDir)) fs.mkdirSync(lightDir);
  if (!fs.existsSync(darkDir)) fs.mkdirSync(darkDir);

  const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox', '--disable-setuid-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 900 });

  for (const theme of ['light', 'dark']) {
    for (const p of pages) {
      try {
        await page.goto(p.url, { waitUntil: 'networkidle2', timeout: 30000 });
        // Force theme
        await page.evaluate((t) => {
          document.documentElement.setAttribute('data-theme', t);
          // toggle any stored theme preference as well
          localStorage.setItem('userTheme', t);
        }, theme);
        // wait a bit for styles to apply
        await new Promise(r => setTimeout(r, 600));

        const filename = path.join(theme === 'light' ? lightDir : darkDir, `${p.name}.png`);
        await page.screenshot({ path: filename, fullPage: true });
        console.log(`Saved ${filename}`);
      } catch (err) {
        console.error(`Failed ${p.name}:`, err.message);
      }
    }
  }

  await browser.close();
  console.log('Screenshots complete');
})();
