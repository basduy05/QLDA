import brevo from '@getbrevo/brevo';

class EmailService {
    constructor() {
        const apiInstance = new brevo.TransactionalEmailsApi();
        apiInstance.setApiKey(
            brevo.ApiClient.instance.authentications['api-key'],
            process.env.BREVO_API_KEY || ''
        );
        this.apiInstance = apiInstance;
    }

    /**
     * Send simple email
     */
    async sendEmail(to, subject, htmlContent, fromName = 'APERLEX') {
        try {
            const sendSmtpEmail = new brevo.SendSmtpEmail();
            sendSmtpEmail.to = [{ email: to, name: to.split('@')[0] }];
            sendSmtpEmail.from = {
                email: process.env.BREVO_SENDER_EMAIL || 'noreply@example.com',
                name: fromName
            };
            sendSmtpEmail.subject = subject;
            sendSmtpEmail.htmlContent = htmlContent;

            const result = await this.apiInstance.sendTransacEmail(sendSmtpEmail);
            
            return {
                success: true,
                messageId: result.body.messageId
            };
        } catch (error) {
            console.error('Email send error:', error);
            throw error;
        }
    }

    /**
     * Send email to multiple recipients
     */
    async sendBulkEmail(recipients, subject, htmlContent, fromName = 'APERLEX') {
        try {
            const promises = recipients.map(to =>
                this.sendEmail(to, subject, htmlContent, fromName)
            );
            const results = await Promise.all(promises);
            return {
                success: true,
                sent: results.length,
                results
            };
        } catch (error) {
            console.error('Bulk email send error:', error);
            throw error;
        }
    }

    /**
     * Send OTP email
     */
    async sendOtpEmail(email, otp) {
        const htmlContent = `
            <h2>APERLEX - Your OTP Code</h2>
            <p>Your One-Time Password (OTP) is:</p>
            <h1 style="color: #007bff; letter-spacing: 2px;">${otp}</h1>
            <p>This code will expire in 10 minutes.</p>
            <p>If you didn't request this code, please ignore this email.</p>
        `;

        return this.sendEmail(email, 'APERLEX - OTP Code', htmlContent);
    }

    /**
     * Send welcome email
     */
    async sendWelcomeEmail(email, name) {
        const htmlContent = `
            <h2>Welcome to APERLEX, ${name}!</h2>
            <p>We're excited to have you on board.</p>
            <p>Get started by:</p>
            <ul>
                <li>Creating your first project</li>
                <li>Adding team members</li>
                <li>Creating tasks and organizing your work</li>
            </ul>
            <p>
                <a href="${process.env.APP_URL || 'http://localhost'}" 
                   style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    Get Started
                </a>
            </p>
        `;

        return this.sendEmail(email, 'Welcome to APERLEX', htmlContent);
    }

    /**
     * Send password reset email
     */
    async sendPasswordResetEmail(email, resetToken) {
        const resetUrl = `${process.env.APP_URL || 'http://localhost'}/reset-password?token=${resetToken}`;
        
        const htmlContent = `
            <h2>Password Reset Request</h2>
            <p>You requested to reset your password. Click the link below:</p>
            <p>
                <a href="${resetUrl}"
                   style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    Reset Password
                </a>
            </p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this, please ignore this email.</p>
        `;

        return this.sendEmail(email, 'APERLEX - Password Reset', htmlContent);
    }

    /**
     * Send task assignment email
     */
    async sendTaskAssignmentEmail(email, taskTitle, assignedBy, projectName) {
        const htmlContent = `
            <h2>New Task Assigned</h2>
            <p>You have been assigned a new task:</p>
            <p><strong>Task:</strong> ${taskTitle}</p>
            <p><strong>Project:</strong> ${projectName}</p>
            <p><strong>Assigned by:</strong> ${assignedBy}</p>
            <p>
                <a href="${process.env.APP_URL || 'http://localhost'}/tasks"
                   style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    View Task
                </a>
            </p>
        `;

        return this.sendEmail(email, `APERLEX - New Task: ${taskTitle}`, htmlContent);
    }

    /**
     * Send weekly report email
     */
    async sendWeeklyReportEmail(email, reportData) {
        const tasksCompleted = reportData.completed || 0;
        const tasksPending = reportData.pending || 0;
        const messagesReceived = reportData.messages || 0;

        const htmlContent = `
            <h2>APERLEX - Weekly Report</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong>Tasks Completed</strong><br>
                        <h3>${tasksCompleted}</h3>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong>Pending Tasks</strong><br>
                        <h3>${tasksPending}</h3>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong>Messages</strong><br>
                        <h3>${messagesReceived}</h3>
                    </td>
                </tr>
            </table>
            <p>
                <a href="${process.env.APP_URL || 'http://localhost'}/reports"
                   style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;">
                    View Full Report
                </a>
            </p>
        `;

        return this.sendEmail(email, 'APERLEX - Weekly Report', htmlContent);
    }

    /**
     * Send contact email (contact form)
     */
    async sendContactEmail(name, email, subject, message) {
        const htmlContent = `
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> ${name}</p>
            <p><strong>Email:</strong> ${email}</p>
            <p><strong>Subject:</strong> ${subject}</p>
            <p><strong>Message:</strong></p>
            <p>${message.replace(/\n/g, '<br>')}</p>
        `;

        return this.sendEmail(
            process.env.BREVO_SENDER_EMAIL || 'admin@example.com',
            `New Contact: ${subject}`,
            htmlContent,
            'APERLEX Contact Form'
        );
    }
}

export default new EmailService();
