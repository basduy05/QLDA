# 🤖 Google Gemini AI Integration

## Setup Instructions

### 1. Get Google Generative AI API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with Google account
3. Click **Get API Key**
4. Click **Create API key in new project**
5. Copy the API key
6. Save to `.env`

```env
GEMINI_API_KEY=your_api_key_here
```

### 2. Enable Google Generative AI API

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create new project or select existing
3. Search for **Google Generative AI API**
4. Click **Enable**
5. Create credentials (API key)

### 3. Install SDK

```bash
cd server
npm install @google/generative-ai
```

### 4. Use Gemini Service

**Generate AI Response:**
```typescript
import GeminiAiService from '@/services/GeminiAiService';

const response = await GeminiAiService.generateResponse(
    'Help me organize my project tasks'
);
console.log(response.response);
```

**Generate Project Insights:**
```typescript
const insights = await GeminiAiService.generateProjectInsights({
    name: 'Website Redesign',
    totalTasks: 15,
    completed: 5,
    inProgress: 8,
    pending: 2,
    teamSize: 4
});
```

**Generate Task Suggestions:**
```typescript
const suggestions = await GeminiAiService.generateTaskSuggestions({
    name: 'Mobile App',
    currentTasks: ['Design', 'Development', 'Testing'],
    teamMembers: ['Alice', 'Bob', 'Charlie']
});
```

---

## 🔌 API Endpoints

### 1. AI Chat Endpoint

```typescript
// POST /api/ai/messages
router.post('/ai/messages', async (req, res) => {
    try {
        const { message, context } = req.body;
        const response = await GeminiAiService.generateResponse(message, context);
        res.json(response);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});
```

### 2. Project Insights Endpoint

```typescript
// GET /api/ai/insights/:projectId
router.get('/ai/insights/:projectId', async (req, res) => {
    try {
        const project = await Project.findById(req.params.projectId);
        const projectData = {
            name: project.name,
            totalTasks: project.tasks.length,
            completed: project.tasks.filter(t => t.status === 'completed').length,
            inProgress: project.tasks.filter(t => t.status === 'in_progress').length,
            pending: project.tasks.filter(t => t.status === 'pending').length,
            teamSize: project.members.length
        };
        
        const insights = await GeminiAiService.generateProjectInsights(projectData);
        res.json(insights);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});
```

### 3. Generate Report Endpoint

```typescript
// POST /api/ai/report
router.post('/ai/report', async (req, res) => {
    try {
        const { projectId, type } = req.body;
        const reportData = await generateReport(projectId, type);
        const summary = await GeminiAiService.generateReportSummary(reportData);
        res.json(summary);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});
```

---

## 💡 Use Cases

### 1. Smart Task Descriptions

```typescript
// Auto-generate task description from title and notes
const description = await GeminiAiService.generateTaskDescription(
    'Fix login bug',
    'Users getting 403 error on login page'
);

// Returns: "Investigate and resolve 403 Forbidden error on user login page..."
```

### 2. Sentiment Analysis

```typescript
// Analyze user feedback
const analysis = await GeminiAiService.analyzeMessage(
    'This project is moving too slow!'
);

// Returns: { sentiment: 'negative', intent: 'express frustration', urgency: 'high' }
```

### 3. Project Name Suggestions

```typescript
// Auto-suggest project names
const names = await GeminiAiService.generateProjectNameSuggestions(
    'Building a platform for freelancers to find jobs'
);

// Returns: ['FreelanceHub', 'WorkConnect', 'GigMatch', ...]
```

### 4. Multi-turn Conversation

```typescript
// Chat session for extended conversation
const session = await GeminiAiService.startChatSession();

const response1 = await session.sendMessage('What are best practices for project management?');
const response2 = await session.sendMessage('How to manage remote teams?');
// Maintains conversation context
```

---

## 🎯 Frontend Integration

```javascript
import { aiAPI } from '@/api/index';

// Get AI response
async function askAI(question) {
    try {
        const response = await aiAPI.sendMessage(question);
        console.log(response.data.response);
    } catch (error) {
        console.error('AI error:', error);
    }
}

// Get project insights
async function getInsights(projectId) {
    try {
        const response = await aiAPI.getInsights(projectId);
        console.log(response.data.insights);
    } catch (error) {
        console.error('Insights error:', error);
    }
}
```

---

## ⚙️ Advanced Features

### 1. Streaming Responses

```typescript
const result = await model.generateContentStream(prompt);

for await (const chunk of result.stream) {
    const chunkText = chunk.text();
    console.log(chunkText);
}
```

### 2. Vision (Image Analysis)

```typescript
import { GoogleGenerativeAI, Part } from '@google/generative-ai';

async function analyzeImage(imagePath) {
    const imageData = fs.readFileSync(imagePath);
    const base64Image = imageData.toString('base64');
    
    const prompt = "Describe what's in this image";
    
    const result = await model.generateContent([
        {
            inlineData: {
                mimeType: 'image/jpeg',
                data: base64Image,
            },
        },
        prompt,
    ]);
    
    return result.response.text();
}
```

### 3. System Instructions

```typescript
const model = client.getGenerativeModel({
    model: 'gemini-pro',
    systemInstruction: `You are a project management assistant. 
                        Help users organize tasks, generate insights, 
                        and provide productivity tips.`
});
```

### 4. Safety Settings

```typescript
const safetySettings = [
    {
        category: 'HARM_CATEGORY_HARASSMENT',
        threshold: 'BLOCK_MEDIUM_AND_ABOVE',
    },
];

const result = await model.generateContent({
    contents: [{ role: 'user', parts: [{ text: prompt }] }],
    safetySettings,
});
```

---

## 📊 Rate Limits & Quotas

- **Free Tier:** 
  - 60 requests/minute
  - 1,500 requests/day
  - Max tokens per request: 2,048,000

- **Upgrade to Premium:**
  - Unlimited requests
  - Higher token limits
  - Priority support

---

## 🆘 Troubleshooting

### API Key Not Working

```typescript
// Verify API key
if (!process.env.GEMINI_API_KEY) {
    console.error('❌ GEMINI_API_KEY not configured');
    return;
}

// Test connection
const client = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
const model = client.getGenerativeModel({ model: 'gemini-pro' });

try {
    const result = await model.generateContent('Hello');
    console.log('✅ Connection successful');
} catch (error) {
    console.error('❌ Connection failed:', error);
}
```

### Rate Limiting

```typescript
// Implement exponential backoff
async function retryWithBackoff(fn, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await fn();
        } catch (error) {
            if (error.status === 429 && i < maxRetries - 1) {
                const delay = Math.pow(2, i) * 1000;
                await new Promise(resolve => setTimeout(resolve, delay));
            } else {
                throw error;
            }
        }
    }
}
```

### Token Limit Exceeded

```typescript
// Truncate long text
function truncateText(text, maxTokens = 2000) {
    const words = text.split(' ');
    const truncated = words.slice(0, Math.floor(maxTokens * 0.75)).join(' ');
    return truncated + '...';
}
```

---

## 💰 Pricing

- **Gemini API:** Free tier available
- **Gen AI Studio:** Free for prototyping
- **Upgrade:** Pay-as-you-go after free quota

---

## 📚 Resources

- [Google Generative AI Docs](https://ai.google.dev/)
- [API Reference](https://ai.google.dev/tutorials)
- [Models Available](https://ai.google.dev/models)
- [Python SDK](https://ai.google.dev/tutorials/python_quickstart)

---

## ✅ Checklist

- [ ] API key obtained
- [ ] API key added to .env
- [ ] SDK installed
- [ ] GeminiAiService tested
- [ ] Endpoints created
- [ ] Frontend integration done
- [ ] Error handling implemented
- [ ] Rate limiting handled

---

**Gemini AI Integration Complete! 🤖**
