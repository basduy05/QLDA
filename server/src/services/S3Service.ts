// @ts-nocheck
import AWS from 'aws-sdk';

export class S3Service {
    private s3: any;
    private bucket: any;
    private baseUrl: any;

    constructor() {
        AWS.config.update({
            accessKeyId: process.env.AWS_ACCESS_KEY_ID,
            secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
            region: process.env.AWS_REGION || 'us-east-1'
        });

        this.s3 = new AWS.S3();
        this.bucket = process.env.AWS_S3_BUCKET;
        this.baseUrl = process.env.AWS_S3_URL;
    }

    /**
     * Upload file to S3
     */
    async uploadFile(file, folder = 'uploads') {
        try {
            const fileKey = `${folder}/${Date.now()}-${file.originalname}`;

            const params = {
                Bucket: this.bucket,
                Key: fileKey,
                Body: file.buffer,
                ContentType: file.mimetype,
                ACL: 'public-read',
                Metadata: {
                    'original-filename': file.originalname,
                    'uploaded-at': new Date().toISOString()
                }
            };

            const result = await this.s3.upload(params).promise();

            return {
                success: true,
                url: result.Location,
                key: result.Key,
                size: file.size,
                mimetype: file.mimetype,
                originalName: file.originalname
            };
        } catch (error) {
            console.error('S3 upload error:', error);
            throw error;
        }
    }

    /**
     * Upload multiple files
     */
    async uploadMultiple(files, folder = 'uploads') {
        const results = await Promise.all(
            files.map(file => this.uploadFile(file, folder))
        );
        return results;
    }

    /**
     * Delete file from S3
     */
    async deleteFile(key) {
        try {
            const params = {
                Bucket: this.bucket,
                Key: key
            };

            await this.s3.deleteObject(params).promise();

            return {
                success: true,
                message: 'File deleted successfully'
            };
        } catch (error) {
            console.error('S3 delete error:', error);
            throw error;
        }
    }

    /**
     * Delete multiple files
     */
    async deleteMultiple(keys) {
        try {
            const params = {
                Bucket: this.bucket,
                Delete: {
                    Objects: keys.map(key => ({ Key: key }))
                }
            };

            const result = await this.s3.deleteObjects(params).promise();
            return {
                success: true,
                deleted: result.Deleted.length,
                errors: result.Errors || []
            };
        } catch (error) {
            console.error('S3 delete multiple error:', error);
            throw error;
        }
    }

    /**
     * Get file URL with expiration
     */
    getSignedUrl(key, expiresIn = 3600) {
        try {
            const params = {
                Bucket: this.bucket,
                Key: key,
                Expires: expiresIn
            };

            const url = this.s3.getSignedUrl('getObject', params);
            return url;
        } catch (error) {
            console.error('S3 get signed URL error:', error);
            throw error;
        }
    }

    /**
     * List files in folder
     */
    async listFiles(prefix = '') {
        try {
            const params = {
                Bucket: this.bucket,
                Prefix: prefix,
                MaxKeys: 1000
            };

            const result = await this.s3.listObjectsV2(params).promise();

            return {
                success: true,
                files: result.Contents || [],
                count: result.Contents?.length || 0
            };
        } catch (error) {
            console.error('S3 list files error:', error);
            throw error;
        }
    }

    /**
     * Copy file
     */
    async copyFile(sourceKey, destinationKey) {
        try {
            const params = {
                Bucket: this.bucket,
                CopySource: `${this.bucket}/${sourceKey}`,
                Key: destinationKey,
                ACL: 'public-read'
            };

            const result = await this.s3.copyObject(params).promise();

            return {
                success: true,
                key: destinationKey,
                etag: result.CopyObjectResult.ETag
            };
        } catch (error) {
            console.error('S3 copy file error:', error);
            throw error;
        }
    }
}

export default new S3Service();
