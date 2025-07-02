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