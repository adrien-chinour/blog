import hljs from 'highlight.js';
import onPageLoaded from "./loader";

import('highlight.js/styles/github-dark.css');

hljs.registerLanguage('javascript', require('highlight.js/lib/languages/javascript'));
hljs.registerLanguage('php', require('highlight.js/lib/languages/php'));
hljs.registerLanguage('twig', require('highlight.js/lib/languages/twig'));
hljs.registerLanguage('yaml', require('highlight.js/lib/languages/yaml'));
hljs.registerLanguage('bash', require('highlight.js/lib/languages/bash'));
hljs.registerLanguage('json', require('highlight.js/lib/languages/json'));
hljs.registerLanguage('diff', require('highlight.js/lib/languages/diff'));
hljs.registerLanguage('dockerfile', require('highlight.js/lib/languages/dockerfile'));

onPageLoaded(() => {
  hljs.highlightAll();
});
