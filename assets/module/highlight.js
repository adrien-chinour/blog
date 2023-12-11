import Prism from 'prismjs/components/prism-core';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-diff';
import 'prismjs/components/prism-yaml';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/themes/prism-tomorrow.min.css';
import onPageLoaded from "./loader";


onPageLoaded().then(() => {
  Prism.highlightAll();
});
