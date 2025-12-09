# Getting Started

This tutorial will guide you through setting up the blog API gateway project from scratch.

## Prerequisites

Before you begin, ensure you have the following installed:

- **Docker** (with Compose support)
- **Make** utility
- **Git**

## Step 1: Clone the Repository

```sh
git clone https://github.com/adrien-chinour/blog.git
cd blog
```

## Step 2: Configure Environment

Create your local environment configuration file:

```sh
cp .env .env.local
```

Edit `.env.local` and configure the required environment variables. For development, you can use InMemory repository implementations which don't require external services.

## Step 3: Install Dependencies

Build the Docker containers and install PHP dependencies:

```sh
make install
```

This command will:
- Build the Docker images
- Start the containers
- Install Composer dependencies

## Step 4: Verify Installation

Once installation completes, the project will be available at:

```
http://localhost:8080
```

You can verify it's working by visiting the home endpoint:

```bash
curl http://localhost:8080/
```

You should receive a JSON response: `{"status":"ok"}`

## Next Steps

- Learn about the [project architecture](../explanation/architecture.md)
- Explore [how to use the Makefile commands](../how-to-guides/using-makefile.md)
- Check the [API endpoints reference](../reference/api-endpoints.md)

