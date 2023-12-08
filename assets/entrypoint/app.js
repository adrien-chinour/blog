// used on all pages
import '../styles/app.scss';
import '../bootstrap';
import '../module/analytics';
import '../module/observability';

// Used by article pages
import '../styles/content.scss';
import '../module/highlight';


import { registerReactControllerComponents } from '@symfony/ux-react';

registerReactControllerComponents(require.context('../react/controllers', true, /\.(j|t)sx?$/));
