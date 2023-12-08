import Countly from 'countly-sdk-web'

if (process.env.NODE_ENV === 'production') {
  window.addEventListener('turbo:load', () => {
    Countly.init({
      app_key: '8696ce4d4dfb160bb24351cb04ae16be868501f6',
      url: 'https://countly.chinour.dev',
      debug: process.env.NODE_ENV !== 'production',
    });

    // Enable tracking
    Countly.track_sessions();
    Countly.track_pageview();
    Countly.track_errors();

    // User click on any article suggestion at the end of article
    document.querySelectorAll('.analytics-suggestions').forEach((link) => {
      link.addEventListener('click', () => {
        console.debug('Suggestion click');
        Countly.q.push(['add_event', {key: 'suggestion_click'}]);
      })
    });
  });
}
