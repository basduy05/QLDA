# 📧 Brevo Email Service Integration

## Setup Instructions

### 1. Create Brevo Account

1. Go to [Brevo.com](https://www.brevo.com)
2. Sign up for free account
3. Complete email verification
4. Verify sender email (your domain email)

### 2. Get API Key

1. Log in to Brevo dashboard
2. Go to **Settings** → **API & Apps** → **SMTP & API**
3. Copy your **API Key** (v3)
4. Save to `.env`

```env
BREVO_API_KEY=your_api_key_here
BREVO_SENDER_EMAIL=noreply@yourdomain.com
BREVO_SENDER_NAME=APERLEX
```

### 3. Verify Sender Email

**In Brevo Dashboard:**
1. Go to **Senders & Domains**
2. Click **Add a Domain**
3. Enter your domain
4. Verify ownership (via DNS TXT record)
5. Add sender email

**DNS Configuration:**
```
Type: MX
Host: yourdomain.com
Value: mail.brevo.com
Priority: 10
```

### 4. Install Brevo SDK

```bash
cd server
npm install @getbrevo/brevo
```

### 5. Use EmailService

**Send OTP Email:**
```typescript
import EmailService from '@/services/EmailService';

// In auth controller
const otp = generateOTP();
await EmailService.sendOtpEmail(email, otp);
```

**Send Welcome Email:**
```typescript
await EmailService.sendWelcomeEmail(newUser.email, newUser.name);
```

**Send Task Assignment:**
```typescript
await EmailService.sendTaskAssignmentEmail(
    userEmail,
    taskTitle,
    assignedByName,
    projectName
);
```

**Send Weekly Report:**
```typescript
await EmailService.sendWeeklyReportEmail(email, {
    completed: 5,
    pending: 3,
    messages: 12
});
```

### 6. Create Email Templates (Optional)

**In Brevo Dashboard:**
1. Go to **Campaigns** → **Templates**
2. Create templates for:
   - OTP verification
   - Welcome email
   - Task notification
   - Weekly report
   - Password reset

Use template IDs in your code:
```typescript
const sendSmtpEmail = new SendSmtpEmail();
sendSmtpEmail.templateId = 123; // Your template ID
```

---

## 🎨 Email Template Examples

### OTP Email Template

```html
<h2>APERLEX - Verification Code</h2>
<p>Your verification code is:</p>
<h1 style="color: #007bff; font-size: 32px; letter-spacing: 3px;">{{OTP_CODE}}</h1>
<p style="color: #666;">This code will expire in 10 minutes.</p>
<p style="color: #999; font-size: 12px;">If you didn't request this code, please ignore this email.</p>
```

### Welcome Email Template

```html
<h2>Welcome to APERLEX, {{USER_NAME}}!</h2>
<p>We're excited to have you on board.</p>

<h3>Get Started:</h3>
<ul>
    <li>Create your first project</li>
    <li>Add team members</li>
    <li>Organize your tasks</li>
</ul>

<p>
    <a href="{{APP_URL}}" 
       style="background-color: #007bff; color: white; padding: 12px 24px; 
              text-decoration: none; border-radius: 5px; display: inline-block;">
        Get Started
    </a>
</p>
```

### Task Assignment Email

```html
<h2>New Task Assigned</h2>
<p>Hi {{USER_NAME}},</p>
<p>You have been assigned a new task:</p>

<div style="border: 1px solid #ddd; padding: 15px; margin: 20px 0;">
    <p><strong>Task:</strong> {{TASK_TITLE}}</p>
    <p><strong>Project:</strong> {{PROJECT_NAME}}</p>
    <p><strong>Assigned by:</strong> {{ASSIGNED_BY}}</p>
    <p><strong>Priority:</strong> {{PRIORITY}}</p>
    <p><strong>Due Date:</strong> {{DUE_DATE}}</p>
</div>

<p>
    <a href="{{TASK_URL}}" 
       style="background-color: #28a745; color: white; padding: 12px 24px; 
              text-decoration: none; border-radius: 5px;">
        View Task
    </a>
</p>
```

---

## 📊 Email Tracking

### Enable Open Tracking

```typescript
const sendSmtpEmail = new SendSmtpEmail();
sendSmtpEmail.to = [{email: userEmail}];
sendSmtpEmail.trackOpens = true;  // Enable open tracking
sendSmtpEmail.htmlContent = html;

await apiInstance.sendTransacEmail(sendSmtpEmail);
```

### Enable Click Tracking

```typescript
sendSmtpEmail.trackClicks = true;  // Enable click tracking
```

### Access Analytics

In Brevo Dashboard:
1. Go to **Reports**
2. View email statistics
3. Check open rates, click rates, etc.

---

## 🔄 Bulk Email Campaign

```typescript
import EmailService from '@/services/EmailService';

// Send to multiple users
const userEmails = ['user1@example.com', 'user2@example.com'];
const result = await EmailService.sendBulkEmail(
    userEmails,
    'Weekly Project Update',
    htmlContent
);

console.log(`Sent to ${result.sent} users`);
```

---

## ⏰ Scheduled Emails

**Setup weekly report sending:**

```typescript
// In your cron job scheduler
import cron from 'node-cron';
import EmailService from '@/services/EmailService';

// Every Friday at 5 PM
cron.schedule('0 17 * * 5', async () => {
    const users = await User.find();
    
    for (const user of users) {
        const reportData = await generateWeeklyReport(user.id);
        await EmailService.sendWeeklyReportEmail(user.email, reportData);
    }
    
    console.log('Weekly reports sent');
});
```

---

## 🆘 Troubleshooting

### Email Not Sending

```typescript
// Check API key
console.log('API Key:', process.env.BREVO_API_KEY);

// Test API connection
const apiKey = process.env.BREVO_API_KEY;
const client = new brevo.ApiClient();
client.authentications['api-key'].apiKey = apiKey;
```

### Bounced Emails

In Brevo Dashboard:
1. Go to **Statistics**
2. Check bounced emails
3. Remove from mailing list
4. Implement bounce handling

### Rate Limiting

```typescript
// Brevo free plan: 300 emails/day
// For higher volume, upgrade plan

// Implement queue if needed
import Bull from 'bull';

const emailQueue = new Bull('email');

emailQueue.process(async (job) => {
    const { email, subject, html } = job.data;
    await EmailService.sendEmail(email, subject, html);
});

// Add to queue
await emailQueue.add({ email, subject, html });
```

---

## 📈 Best Practices

### 1. Email Verification

```typescript
// Always verify email before sending
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
if (!emailRegex.test(email)) {
    throw new Error('Invalid email');
}
```

### 2. Unsubscribe Link

Add to all marketing emails:
```html
<p>
    <a href="{{UNSUBSCRIBE_LINK}}">Unsubscribe</a>
</p>
```

### 3. Error Handling

```typescript
try {
    await EmailService.sendEmail(email, subject, html);
} catch (error) {
    if (error.response?.status === 429) {
        // Rate limited - add to queue for retry
        await emailQueue.add({ email, subject, html });
    } else {
        // Log error
        console.error('Email send failed:', error);
    }
}
```

### 4. Test Before Sending

```typescript
// Test email configuration
const testResult = await EmailService.sendEmail(
    'test@example.com',
    'Test Email',
    '<h1>Test</h1>'
);

if (testResult.success) {
    console.log('✅ Email service working');
} else {
    console.error('❌ Email service failed');
}
```

---

## 💰 Pricing

- **Free Plan:** 300 emails/day
- **Lite Plan:** 2,000-5,000 emails/month (~$25)
- **Standard:** 50,000+ emails/month (~$99)
- **Premium:** Unlimited + Phone support

---

## 📞 Resources

- [Brevo Documentation](https://developers.brevo.com/)
- [Email Best Practices](https://developers.brevo.com/docs/sending-an-smtp-transactional-email)
- [API Reference](https://developers.brevo.com/reference)

---

**Brevo Email Setup Complete! 📧**
