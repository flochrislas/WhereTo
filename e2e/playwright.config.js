// Playwright Test configuration (https://playwright.dev/docs/test-configuration).
// The app must already be running: BASE_URL points at it (default: the dev stack of dev/compose.yaml).
// CI starts "php artisan serve" and sets BASE_URL=http://127.0.0.1:8000 (.github/workflows/ci.yml).
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',
  // The scenarios are independent, so they can run in parallel
  fullyParallel: true,
  // On CI a flaky test gets one more try; locally a failure is a failure
  retries: process.env.CI ? 1 : 0,
  reporter: process.env.CI ? [['list'], ['html', { open: 'never' }]] : 'list',
  use: {
    baseURL: process.env.BASE_URL || 'http://localhost:8010',
    // Shibuya: GMO Internet tower, where the author worked (300 m from the station)
    geolocation: { latitude: 35.656113, longitude: 139.699425 },
    permissions: ['geolocation'],
    // Keep a trace of a failed test (open it with "npx playwright show-trace")
    trace: 'retain-on-failure',
  },
  projects: [
    // A phone: one panel at a time
    { name: 'phone', use: { ...devices['Pixel 7'] } },
    // A PC: the three panels side by side (style.css, "wide screens")
    { name: 'desktop', use: { ...devices['Desktop Chrome'], viewport: { width: 1280, height: 800 } } },
    // The same two in WebKit, the engine of Safari: every browser of an iPhone, and Safari on a Mac
    { name: 'safari-phone', use: { ...devices['iPhone 15'] } },
    { name: 'safari-desktop', use: { ...devices['Desktop Safari'], viewport: { width: 1280, height: 800 } } },
    // Firefox is not part of the usual run: "npm run test:firefox", by hand, before an important release
    ...(process.env.FIREFOX ? [{ name: 'firefox-desktop', use: { ...devices['Desktop Firefox'], viewport: { width: 1280, height: 800 } } }] : []),
  ],
});
