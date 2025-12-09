import http from 'k6/http';
import { check } from 'k6';
import { randomItem } from 'https://jslib.k6.io/k6-utils/1.2.0/index.js';

const paths = [
  '/articles',
  '/features',
  '/projects',
  '/articles/@id',
  '/articles/@id/comments',
  '/articles/@id/recommendations',
];

const articles = [
  'NMVntUf1yiwhMkrOftLNB',
  '4aAkSjsn311n0jwAgNnIvH',
  '7rLwY85ICqoRIXKyHeeSGY',
  '1UXgm0ZL7D1rysOnysYlrM',
  '1yN0AOunIiaLn8nXZc7k6z',
  '4EXN4ffKnY92zPpVS1xrVn',
  '6J0hWoFCuiuWQtjp3K1mXn',
];

export const options = {
  vus: 50,
  duration: '1m',
};

export default function () {
  const path = randomItem(paths).replace('@id', randomItem(articles));
  const res = http.get(`https://api.udfn.fr${path}`);
  check(res, {
    'is status 200': (r) => r.status === 200,
  });
}
