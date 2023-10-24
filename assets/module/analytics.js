import * as ackeeTracker from 'ackee-tracker';

window.addEventListener('DOMContentLoaded', () => {
  const ackeeConfigElement = document.querySelector('[data-ackee-domain-id]')
  if (ackeeConfigElement == null) return;

  const ackeeInstance = ackeeTracker.create(
    ackeeConfigElement.getAttribute('data-ackee-server') || '',
    JSON.parse(ackeeConfigElement.getAttribute('data-ackee-opts') || '{}')
  );

  ackeeInstance.record(ackeeConfigElement.getAttribute('data-ackee-domain-id'));

  document.querySelectorAll('.analytics-suggestions').forEach((e) => {
    e.addEventListener('click', () => {
      ackeeInstance.action('3ef7a44b-8e08-4588-92ae-b8a933dd92cb', {
        key: 'Suggestion',
        value: 1
      });
    })
  })
})
