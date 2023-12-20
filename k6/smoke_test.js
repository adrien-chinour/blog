import http from 'k6/http';
import {check, sleep} from 'k6';
import {randomItem} from 'https://jslib.k6.io/k6-utils/1.2.0/index.js';

export const options = {
  vus: 2,
  duration: '30s',
  thresholds: {
    http_req_failed: ['rate<0.01'], // http errors should be less than 1%
    http_req_duration: ['p(90)<500'], // 90 percent of response times must be below 200ms
    checks: ['rate>0.99'], // the rate of successful checks should be higher than 99%
  },
};

const routes = [
  '/',
  '/articles/',
  '/projets/',
  '/article/migration-vers-php-8.html',
  '/article/early-hints-en-php-comment-ca-marche.html',
  '/article/combien-de-temps-avez-vous-passe-sur-netflix.html',
  '/article/les-structures-de-donnees-en-php.html'
]

export default function () {
  const res = http.get(`https://www.udfn.fr${randomItem(routes)}`);
  check(res, {
    'is status 200': (r) => r.status === 200,
    'is content html': (r) => r.headers['Content-Type'].match(/text\/html/)
  });
  sleep(1);
}
