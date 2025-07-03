# PHP Backend Setup Instructions

This directory contains the PHP backend API for the Sanjeevani website. Follow these instructions to set up the backend on your shared hosting:

1. Upload the following files to your hosting:
   - All files in the `/api` directory
   - The `composer.json` file
   - Your `.env` file (or set environment variables in hosting panel)

2. Install Dependencies:
   ```bash
   composer install
   ```

3. Environment Variables:
   Set the following environment variables in your hosting control panel:
   - `NEXT_PUBLIC_RAZORPAY_KEY_ID`
   - `RAZORPAY_KEY_SECRET`

4. File Permissions:
   ```bash
   chmod 755 api/
   chmod 644 api/*.php
   chmod 644 api/.htaccess
   ```

5. Update Frontend:
   Update your frontend API endpoints to point to your new PHP backend URLs:
   ```javascript
   // Example:
   const API_URL = 'https://your-domain.com/api';
   ```

## Important Notes:
- Make sure your hosting supports PHP 7.4 or higher
- Enable the following PHP extensions:
  - json
  - openssl
  - curl
- Set up HTTPS for secure transactions
- Error logging is disabled for privacy
- Keep your .env file secure and never commit it to version control
- User registration data is not stored locally to comply with privacy practices

# Sanjeevani Backend

Backend API for the Sanjeevani payment system, integrated with Razorpay.

## Deployment on Render.com

### Prerequisites

- A Render.com account
- Razorpay API keys (Key ID and Key Secret)

### Deployment Steps

1. Fork or clone this repository to your GitHub account
2. In Render Dashboard, click "New" and select "Web Service"
3. Connect your GitHub repository
4. Select "Docker" as the Environment
5. Configure the service with the following settings:
   - Name: sanjeevani-backend (or your preferred name)
   - Environment Variables:
     - NEXT_PUBLIC_RAZORPAY_KEY_ID: Your Razorpay Key ID
     - RAZORPAY_KEY_SECRET: Your Razorpay Key Secret
6. Click "Create Web Service"

The deployment will automatically use the DockerFile in the repository to build and deploy the service.

### API Endpoints

- `/api/create-order.php` - Creates a new payment order
- `/api/verify-payment.php` - Verifies a payment after completion

For detailed API documentation, visit the root URL of your deployed service.

## Local Development

To run the service locally:

```bash
# Build the Docker image
docker build -t sanjeevani-backend .

# Run the container
docker run -p 8080:80 -e NEXT_PUBLIC_RAZORPAY_KEY_ID=your_key_id -e RAZORPAY_KEY_SECRET=your_key_secret sanjeevani-backend
```

Then access the API at http://localhost:8080 