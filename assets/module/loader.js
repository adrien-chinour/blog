/**
 * Provide a utils function to add callback to DOMContentLoaded or turbo:render
 */
export default async function onPageLoaded() {
  return new Promise((resolve) => {
    window.addEventListener('turbo:render', () => {
      resolve();
    });
    window.addEventListener('DOMContentLoaded', () => {
      resolve();
    });
  });
}
