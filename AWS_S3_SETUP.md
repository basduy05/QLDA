# 🔧 AWS S3 File Upload Integration

## Setup Instructions

### 1. AWS Account & S3 Bucket Creation

**Create S3 Bucket:**
1. Go to [AWS S3 Console](https://s3.console.aws.amazon.com)
2. Click "Create Bucket"
3. Name: `aperlex-files-prod` (or similar)
4. Region: Choose closest to your users
5. Block public access: Uncheck "Block all public access"
6. Create bucket

### 2. Create IAM User for S3 Access

1. Go to [IAM Console](https://console.aws.amazon.com/iam/)
2. Click "Users" → "Create User"
3. Username: `aperlex-s3-user`
4. Create user

**Add S3 Permissions:**
1. Click on the user → "Add permissions"
2. Click "Attach policies directly"
3. Search and select: `AmazonS3FullAccess`
4. Review and add

**Generate Access Keys:**
1. Click on user → "Security credentials" tab
2. Click "Create access key"
3. Select "Application running outside AWS"
4. Download `.csv` file
5. **Save these securely!**

### 3. Configure Backend

**Update `.env` in server:**
```env
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_access_key
AWS_REGION=us-east-1
AWS_S3_BUCKET=aperlex-files-prod
AWS_S3_URL=https://aperlex-files-prod.s3.amazonaws.com
```

### 4. Install AWS SDK

```bash
cd server
npm install aws-sdk
```

### 5. Use S3Service in Your Code

**Controller example:**
```typescript
import S3Service from '@/services/S3Service';

// Upload single file
router.post('/projects/:id/upload', async (req, res) => {
    try {
        const result = await S3Service.uploadFile(req.file, 'projects');
        res.json({ success: true, url: result.url });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// Upload multiple files
router.post('/tasks/:id/attachments', async (req, res) => {
    try {
        const results = await S3Service.uploadMultiple(req.files, 'task-attachments');
        res.json({ success: true, files: results });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// Delete file
router.delete('/files/:key', async (req, res) => {
    try {
        const result = await S3Service.deleteFile(req.params.key);
        res.json(result);
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});
```

### 6. Frontend Usage

```javascript
import { taskAPI } from '@/api/index';

// Upload task attachment
const file = event.target.files[0];
const response = await taskAPI.uploadAttachment(taskId, file);
console.log('Uploaded:', response.data.url);
```

---

## 💡 Best Practices

### 1. Folder Organization
```
s3://aperlex-files-prod/
├── projects/           # Project logos/images
├── task-attachments/   # Task files
├── user-avatars/       # User profile pictures
├── exports/            # Excel/PDF exports
└── temp/              # Temporary files
```

### 2. File Size Limits

```typescript
// In your middleware
const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB

if (req.file.size > MAX_FILE_SIZE) {
    return res.status(413).json({ error: 'File too large' });
}
```

### 3. Security

- ✅ Use signed URLs for private files
- ✅ Validate file types
- ✅ Scan for malware
- ✅ Encrypt sensitive files
- ✅ Set object ACLs to private by default

---

## 📊 Cost Optimization

### 1. Lifecycle Policies

Delete old files automatically:

```bash
# AWS CLI
aws s3api put-bucket-lifecycle-configuration \
  --bucket aperlex-files-prod \
  --lifecycle-configuration file://lifecycle.json
```

**lifecycle.json:**
```json
{
  "Rules": [
    {
      "Id": "DeleteOldTempFiles",
      "Status": "Enabled",
      "Prefix": "temp/",
      "Expiration": {
        "Days": 7
      }
    },
    {
      "Id": "TransitionOldExports",
      "Status": "Enabled",
      "Prefix": "exports/",
      "NoncurrentVersionTransitions": [
        {
          "NoncurrentDays": 30,
          "StorageClass": "GLACIER"
        }
      ]
    }
  ]
}
```

### 2. CloudFront CDN (Optional)

For better performance, use CloudFront:

```bash
# Setup CloudFront distribution pointing to S3
# This caches files globally and reduces bandwidth costs
```

---

## 🆘 Troubleshooting

### Access Denied Error

```bash
# Check IAM permissions
# Make sure user has S3FullAccess

# Check bucket policy
aws s3api get-bucket-policy --bucket aperlex-files-prod
```

### Upload Timeout

```typescript
// Increase upload timeout
const s3 = new AWS.S3({
    httpOptions: {
        timeout: 300000 // 5 minutes
    }
});
```

### Signed URL Expired

```typescript
// Generate signed URL with custom expiry
S3Service.getSignedUrl(key, 7200); // 2 hours
```

---

## 📈 Monitoring

### CloudWatch Metrics

```bash
# AWS CLI
aws cloudwatch get-metric-statistics \
  --namespace AWS/S3 \
  --metric-name NumberOfObjects \
  --dimensions Name=BucketName,Value=aperlex-files-prod \
  --start-time 2024-01-01T00:00:00Z \
  --end-time 2024-01-31T23:59:59Z \
  --period 86400 \
  --statistics Sum
```

### Enable Logging

```bash
aws s3api put-bucket-logging \
  --bucket aperlex-files-prod \
  --bucket-logging-status file://logging.json
```

---

## 💰 Estimated Costs (per month)

- Storage: $0.023 per GB → ~$2.30 for 100GB
- Upload: Free
- Download: $0.09 per GB → ~$9.00 for 100GB
- **Total: ~$11/month for typical usage**

---

**S3 Setup Complete! ☁️**
