# How to Use Bruno API Collection

This guide explains how to use the Bruno API collection to test and explore API endpoints.

## Prerequisites

Install [Bruno](https://www.usebruno.com/) on your system.

## Setup

1. Open Bruno application
2. Click "Open Collection" or use File → Open Collection
3. Navigate to `docs/bruno/bruno.json` in the project directory
4. The collection will load with all available API requests

## Configuring Environments

The collection includes two environments:

- **dev**: `http://localhost:8080`
- **prod**: Production API URL

To configure environments:

1. Open the environments panel in Bruno
2. Edit `docs/bruno/environments/dev.bru` or `prod.bru`
3. Set the `host` variable to your API URL
4. Configure secret variables (tokens, API keys) in the secret section

## Available Collections

The Bruno collection includes:

- **Articles**: Get articles, search, recommendations
- **Comments**: Get and post comments
- **Projects**: Get projects list
- **Pages**: Get static pages
- **Features**: Feature flags management
- **Cache**: Cache invalidation endpoints

## Using the Collection

1. Select an environment (dev/prod)
2. Choose a request from the collection
3. Click "Send" to execute the request
4. View the response in the response panel

## See Also

- [API Endpoints Reference](../reference/api-endpoints.md)

