import * as ackeeTracker from 'ackee-tracker';
import onPageLoaded from "./loader";

onPageLoaded().then(() => {
  const ackeeConfigElement = document.querySelector('[data-ackee-domain-id]')
  if (ackeeConfigElement == null) return;

  const ackeeInstance = ackeeTracker.create(
    ackeeConfigElement.getAttribute('data-ackee-server') || '',
    JSON.parse(ackeeConfigElement.getAttribute('data-ackee-opts') || '{}')
  );

  ackeeInstance.record(ackeeConfigElement.getAttribute('data-ackee-domain-id'));

  // Suggestion event on analytics-suggestions links clicked
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
