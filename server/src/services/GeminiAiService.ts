// @ts-nocheck
import { GoogleGenerativeAI } from '@google/generative-ai';

export class GeminiAiService {
    private client: any;
    private model: any;

    constructor() {
        const apiKey = process.env.GEMINI_API_KEY;
        if (!apiKey) {
            console.warn('⚠️ GEMINI_API_KEY not configured');
        }
        this.client = new GoogleGenerativeAI(apiKey || '');
        this.model = this.client.getGenerativeModel({ model: 'gemini-pro' });
    }

    /**
     * Generate AI response to a message
     */
    async generateResponse(message, context = '') {
        try {
            const prompt = context 
                ? `Context: ${context}\n\nUser: ${message}`
                : message;

            const result = await this.model.generateContent(prompt);
            const response = await result.response;
            const text = response.text();

            return {
                success: true,
                response: text,
                tokens: {
                    input: result.totalTokens,
                    output: 0
                }
            };
        } catch (error) {
            console.error('Gemini generation error:', error);
            throw error;
        }
    }

    /**
     * Generate project insights
     */
    async generateProjectInsights(projectData) {
        try {
            const prompt = `
                Analyze this project data and provide insights:
                - Project: ${projectData.name}
                - Total Tasks: ${projectData.totalTasks}
                - Completed: ${projectData.completed}
                - In Progress: ${projectData.inProgress}
                - Pending: ${projectData.pending}
                - Team Size: ${projectData.teamSize}
                
                Provide insights on project health, productivity, and recommendations.
            `;

            const result = await this.generateResponse(prompt);
            return result;
        } catch (error) {
            console.error('Gemini project insights error:', error);
            throw error;
        }
    }

    /**
     * Generate task suggestions
     */
    async generateTaskSuggestions(projectData) {
        try {
            const prompt = `
                Based on this project:
                - Name: ${projectData.name}
                - Current Tasks: ${projectData.currentTasks}
                - Team: ${projectData.teamMembers?.join(', ')}
                
                Suggest next tasks or improvements for this project.
                Provide 3-5 actionable suggestions.
            `;

            const result = await this.generateResponse(prompt);
            return result;
        } catch (error) {
            console.error('Gemini task suggestions error:', error);
            throw error;
        }
    }

    /**
     * Generate report summary
     */
    async generateReportSummary(reportData) {
        try {
            const prompt = `
                Summarize this report data:
                - Period: ${reportData.period}
                - Tasks Completed: ${reportData.completed}
                - Tasks Pending: ${reportData.pending}
                - Team Activity: ${reportData.activity}
                - Issues: ${reportData.issues?.join(', ')}
                
                Create an executive summary with key metrics and recommendations.
            `;

            const result = await this.generateResponse(prompt);
            return result;
        } catch (error) {
            console.error('Gemini report summary error:', error);
            throw error;
        }
    }

    /**
     * Analyze user message for sentiment and intent
     */
    async analyzeMessage(message) {
        try {
            const prompt = `
                Analyze this message for sentiment and intent:
                "${message}"
                
                Respond in JSON format with:
                {
                    "sentiment": "positive|negative|neutral",
                    "intent": "description of what user wants",
                    "urgency": "low|medium|high"
                }
            `;

            const result = await this.generateResponse(prompt);
            
            try {
                const json = JSON.parse(result.response);
                return { success: true, analysis: json };
            } catch {
                return { success: true, analysis: { raw: result.response } };
            }
        } catch (error) {
            console.error('Gemini message analysis error:', error);
            throw error;
        }
    }

    /**
     * Generate task description from user input
     */
    async generateTaskDescription(title, notes) {
        try {
            const prompt = `
                Based on this task title and notes, generate a professional task description:
                - Title: ${title}
                - Notes: ${notes}
                
                Generate a clear, actionable task description.
            `;

            const result = await this.generateResponse(prompt);
            return result;
        } catch (error) {
            console.error('Gemini task description error:', error);
            throw error;
        }
    }

    /**
     * Generate project name suggestions
     */
    async generateProjectNameSuggestions(description) {
        try {
            const prompt = `
                Based on this project description, suggest 5 project names:
                "${description}"
                
                Provide creative, professional project names. Return as JSON array.
            `;

            const result = await this.generateResponse(prompt);
            return result;
        } catch (error) {
            console.error('Gemini project naming error:', error);
            throw error;
        }
    }

    /**
     * Chat conversation (multi-turn)
     */
    async startChatSession() {
        try {
            const chatSession = this.model.startChat({
                history: [],
                generationConfig: {
                    maxOutputTokens: 1024,
                }
            });

            return {
                success: true,
                session: chatSession,
                sendMessage: async (message) => {
                    try {
                        const result = await chatSession.sendMessage(message);
                        const response = await result.response;
                        return {
                            success: true,
                            response: response.text()
                        };
                    } catch (error) {
                        return {
                            success: false,
                            error: error.message
                        };
                    }
                }
            };
        } catch (error) {
            console.error('Gemini chat session error:', error);
            throw error;
        }
    }
}

export default new GeminiAiService();
