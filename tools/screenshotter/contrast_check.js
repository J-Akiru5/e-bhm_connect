const puppeteer = require('puppeteer');
const axeSource = require('axe-core').source;
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
  const results = {};
  const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox'] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 900 });

  for (const theme of ['light', 'dark']) {
    results[theme] = [];
    for (const p of pages) {
      try {
        await page.goto(p.url, { waitUntil: 'networkidle2', timeout: 30000 });

        // Force theme and localStorage
        await page.evaluate((t) => {
          document.documentElement.setAttribute('data-theme', t);
          localStorage.setItem('userTheme', t);
        }, theme);

        // Inject axe-core
        await page.addScriptTag({ content: axeSource });

        // Run only color-contrast rule
        const res = await page.evaluate(async () => {
          return await axe.run(document, { runOnly: { type: 'rule', values: ['color-contrast'] } });
        });

        const violations = res.violations || [];

        const simplified = violations.map(v => ({
          id: v.id,
          impact: v.impact,
          help: v.help,
          nodes: v.nodes.map(n => ({
            html: n.html,
            target: n.target,
            failureSummary: n.failureSummary
          }))
        }));

        results[theme].push({ page: p.name, url: p.url, issues: simplified, total: simplified.length });

        console.log(`Theme=${theme} Page=${p.name} contrast issues=${simplified.length}`);
      } catch (err) {
        console.error(`Error checking ${p.name} (${theme}):`, err.message);
        results[theme].push({ page: p.name, url: p.url, error: err.message });
      }
    }
  }

  await browser.close();

  const outPath = path.resolve(__dirname, '../contrast-results.json');
  fs.writeFileSync(outPath, JSON.stringify(results, null, 2), 'utf8');
  console.log('Contrast results saved to', outPath);
})();