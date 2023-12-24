import * as ackeeTracker from 'ackee-tracker';

window.addEventListener('turbo:load', () => {
  const ackeeInstance = ackeeTracker.create('https://analytics.chinour.dev', {});
  ackeeInstance.record('2a5cd48f-fc36-4f6e-8840-0db972af81c7');

  // User click on any article suggestion at the end of article
  document.querySelectorAll('.analytics-suggestions').forEach((link) => {
    link.addEventListener('click', () => {
      console.debug('Suggestion click');

      ackeeInstance.action('e45189f9-dc5b-411a-a50f-5811b67cf15c', {
        key: 'Click',
        value: 1
      });
    })
  });
});
