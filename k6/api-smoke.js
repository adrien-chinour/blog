import http from 'k6/http';
import { check, sleep } from 'k6';

const paths = [
  '/articles',
  '/features',
  '/projects',
  '/articles/@id',
  '/articles/@id/comments',
  '/articles/@id/recommendations',
];

export default function () {
  for (let path of paths) {
    const res = http.get(
      `https://api.udfn.fr${path.replace('@id', 'NMVntUf1yiwhMkrOftLNB')}`,
      {
        headers: {
          Authorization: `Bearer ${__ENV.AUTH_TOKEN}`
        }
      }
    );
    check(res, {
      'is status 200': (r) => r.status === 200,
    });

    sleep(0.1);
  }
}
