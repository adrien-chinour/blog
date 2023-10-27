import {getWebInstrumentations, initializeFaro} from '@grafana/faro-web-sdk';
import {TracingInstrumentation} from '@grafana/faro-web-tracing';

if (process.env.NODE_ENV === 'production') {
  initializeFaro({
    url: 'https://faro-collector-prod-eu-west-0.grafana.net/collect/9689c3ba5a20d52b36dec6a5da24f8eb',
    app: {
      name: 'udfn.fr',
      version: '1.0.0',
      environment: 'production'
    },
    instrumentations: [
      ...getWebInstrumentations(),
      new TracingInstrumentation(),
    ],
  });
}
