# Deployment Configuration

## Environment Separation

This project supports separate configurations for development and production environments.

### Development Environment
- Uses `.env` file with development/testing credentials
- Database: Clever Cloud (development)
- PayHere: Sandbox mode

### Production Environment
- Uses `.env.production` file with live credentials (if present)
- Falls back to `.env` with production overrides
- Database: cPanel hosting database
- PayHere: Live mode

## Setup Instructions

### For Development
1. Keep using your existing `.env` file
2. Your development credentials remain unchanged

### For Production Deployment

#### Option 1: Use .env.production file (Recommended)
1. Copy `.env.production` template to your local project
2. Update it with your actual production credentials:
   - Database: Your cPanel hosting database credentials
   - PayHere: Your live merchant credentials
3. Keep this file secure and never commit it to Git

#### Option 2: Manual Production Setup
1. After deployment, manually edit the `.env` file on your production server
2. Update database and PayHere credentials
3. Set `PAYHERE_SANDBOX=false` for live payments

## Deployment Process

When you push to Git:
1. cPanel deploys your code using `.cpanel.yml`
2. If `.env.production` exists locally, it copies that to production
3. Otherwise, it copies `.env` and overrides key production settings
4. Production files are cleaned up (tests, development files removed)
5. Proper file permissions are set

## Security Notes

- `.env.production` is ignored by Git (never committed)
- Production `.env` file permissions are set to 600 (secure)
- Development files and tests are removed from production
- Database credentials are environment-specific

## Testing

After deployment:
1. Check that your live site loads
2. Test a small payment transaction
3. Verify database connections work
4. Check that debug mode is disabled