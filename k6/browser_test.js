import { browser } from 'k6/experimental/browser';

export const options = {
  scenarios: {
    ui: {
      executor: 'shared-iterations',
      options: {
        browser: {
          type: 'chromium',
        },
      },
    },
  },
  thresholds: {
    'browser_web_vital_fcp': ['p(95) < 400'],
    'browser_web_vital_lcp': ['p(95) < 300'],
    'browser_web_vital_ttfb': ['p(95) < 150'],
    'browser_web_vital_inp': ['p(95) < 80'],
  },
}

export default async function () {
  const page = browser.newPage();

  try {
    // Open homepage
    await page.goto('https://www.udfn.fr/');

    // Click on latest article
    await page.locator('//article[1]/a', {strict: false}).click();
  } finally {
    page.close();
  }
}
