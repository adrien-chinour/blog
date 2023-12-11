# Project behind www.udfn.fr

![docs/images/article.png](docs/images/article.png)

**Sandbox project used to try some stuffs and made good code (_I believe_) on my free time.**

See my blog : [www.udfn.fr](https://www.udfn.fr) (_Yeah I use www. and what ?_ 🤨)

> [!NOTE]
> **~~Fun~~ fact**
> - Time spend on **create content** -> less than 1 day
> - Time spend on **coding this project** 4 weekend.

> [!WARNING]
> This project need a lot of improvement, for stability and security. If for some reason you found a code to reused : *
*be
careful**.

## Documentation

**Documentation is available in docs folder or with [Docsify](https://docsify.js.org) in this
webpage : [adrien-chinour.github.io/blog.](https://adrien-chinour.github.io/blog/)**

## Roadmap

### DX

- [x] Improve Makefile
- [x] Add container for assets (Node 18)
- [x] Better documentation (Docsify)

### Features

- [x] **Secure endpoint for admin actions**
- [ ] Webhook on Contentful (cache invalidation on article update)
- [ ] Add comments on blog posts
- [ ] Add tag list page (need more articles)

### DevOps

- [ ] Add GitHub Actions for Quality Checks
- [x] Cache-Control is not set on js/css files

### Testing

- [ ] PHP Unit test using **Pest**
- [ ] Javascript Unit test using **Jest**
- [ ] PHP Architecture test using **Pest**
- [ ] Performance testing using **k6**
- [ ] e2e testing using **Cypress**
