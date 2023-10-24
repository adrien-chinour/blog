import * as ackeeTracker from 'ackee-tracker';
import onPageLoaded from "./loader";

onPageLoaded(() => {
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

      ackeeInstance.action('3ef7a44b-8e08-4588-92ae-b8a933dd92cb', {
        key: 'Suggestion',
        value: 1
      });
    })
  });
});
