/**
 * Provide a utils function to add callback to DOMContentLoaded or turbo:render
 *
 * @param callback : function
 */
export default function onPageLoaded(callback) {
  window.addEventListener('turbo:render', () => {
    callback();
  });
  window.addEventListener('DOMContentLoaded', () => {
    callback();
  });
}
