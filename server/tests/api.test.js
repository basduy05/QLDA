/**
 * API Testing Suite
 * 
 * Run with: npm run test:api
 * Or with Postman/Insomnia using the provided collection
 */

// Test configuration
const API_BASE = process.env.API_URL || 'http://localhost:3000/api';
const TEST_TIMEOUT = 10000;

// Helper function for API calls
async function apiCall(method, endpoint, data = null, token = null) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options = {
        method,
        headers,
    };

    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        const result = await response.json();
        return {
            status: response.status,
            data: result,
            ok: response.ok,
        };
    } catch (error) {
        throw new Error(`API call failed: ${error.message}`);
    }
}

// Test suites
export const testSuites = {
    // Authentication Tests
    auth: {
        register: async () => {
            console.log('\n📝 Testing User Registration...');
            const response = await apiCall('POST', '/auth/register', {
                name: 'Test User',
                email: `test-${Date.now()}@example.com`,
                password: 'TestPassword123!',
                password_confirmation: 'TestPassword123!'
            });
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        login: async () => {
            console.log('\n🔐 Testing Login...');
            const response = await apiCall('POST', '/auth/login', {
                email: 'test@example.com',
                password: 'password'
            });
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                localStorage.setItem('auth_token', response.data.token);
            }
            return response;
        },

        logout: async (token) => {
            console.log('\n🚪 Testing Logout...');
            const response = await apiCall('POST', '/auth/logout', {}, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        }
    },

    // User Tests
    users: {
        getProfile: async (token) => {
            console.log('\n👤 Testing Get User Profile...');
            const response = await apiCall('GET', '/users/profile', null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        updateProfile: async (token) => {
            console.log('\n✏️ Testing Update User Profile...');
            const response = await apiCall('PUT', '/users/profile', {
                name: 'Updated Name',
                bio: 'Test bio'
            }, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        getAllUsers: async (token) => {
            console.log('\n👥 Testing Get All Users...');
            const response = await apiCall('GET', '/users', null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                console.log(`  Count: ${response.data.data?.length || 0} users`);
            }
            return response;
        }
    },

    // Project Tests
    projects: {
        create: async (token) => {
            console.log('\n📁 Testing Create Project...');
            const response = await apiCall('POST', '/projects', {
                name: `Test Project ${Date.now()}`,
                description: 'Test project description',
                status: 'active'
            }, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        getAll: async (token) => {
            console.log('\n📂 Testing Get All Projects...');
            const response = await apiCall('GET', '/projects', null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                console.log(`  Count: ${response.data.data?.length || 0} projects`);
            }
            return response;
        },

        getById: async (projectId, token) => {
            console.log('\n📋 Testing Get Project by ID...');
            const response = await apiCall('GET', `/projects/${projectId}`, null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        }
    },

    // Task Tests
    tasks: {
        create: async (projectId, token) => {
            console.log('\n✅ Testing Create Task...');
            const response = await apiCall('POST', `/projects/${projectId}/tasks`, {
                title: `Test Task ${Date.now()}`,
                description: 'Test task description',
                priority: 'medium',
                due_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString()
            }, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        getByProject: async (projectId, token) => {
            console.log('\n📝 Testing Get Tasks by Project...');
            const response = await apiCall('GET', `/projects/${projectId}/tasks`, null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                console.log(`  Count: ${response.data.data?.length || 0} tasks`);
            }
            return response;
        },

        update: async (taskId, token) => {
            console.log('\n🔄 Testing Update Task...');
            const response = await apiCall('PUT', `/tasks/${taskId}`, {
                title: 'Updated Task Title',
                status: 'in_progress'
            }, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        }
    },

    // Message Tests
    messages: {
        createConversation: async (userId, token) => {
            console.log('\n💬 Testing Create Conversation...');
            const response = await apiCall('POST', '/conversations', {
                user_id: userId
            }, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        sendMessage: async (conversationId, token) => {
            console.log('\n📨 Testing Send Message...');
            const response = await apiCall('POST', `/conversations/${conversationId}/messages`, {
                message: `Test message at ${new Date().toISOString()}`
            }, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            return response;
        },

        getMessages: async (conversationId, token) => {
            console.log('\n📂 Testing Get Conversation Messages...');
            const response = await apiCall('GET', `/conversations/${conversationId}/messages`, null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                console.log(`  Count: ${response.data.data?.length || 0} messages`);
            }
            return response;
        }
    },

    // Notification Tests
    notifications: {
        getNotifications: async (token) => {
            console.log('\n🔔 Testing Get Notifications...');
            const response = await apiCall('GET', '/notifications', null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                console.log(`  Count: ${response.data.data?.length || 0} notifications`);
            }
            return response;
        },

        getUnreadCount: async (token) => {
            console.log('\n📬 Testing Get Unread Count...');
            const response = await apiCall('GET', '/notifications/unread/count', null, token);
            console.log(`  Status: ${response.status}`);
            console.log(`  Result: ${response.ok ? '✅ PASS' : '❌ FAIL'}`);
            if (response.ok) {
                console.log(`  Unread: ${response.data.count || 0}`);
            }
            return response;
        }
    }
};

// Run all tests
export async function runAllTests(token) {
    console.log('\n' + '='.repeat(50));
    console.log('🚀 APERLEX API TEST SUITE');
    console.log('='.repeat(50));
    console.log(`API URL: ${API_BASE}`);
    console.log(`Token: ${token ? '✅ Provided' : '❌ Missing'}`);
    console.log('='.repeat(50));

    const results = {};

    try {
        // Run each test suite
        for (const [suiteName, tests] of Object.entries(testSuites)) {
            console.log(`\n\n🧪 ${suiteName.toUpperCase()} TESTS`);
            console.log('-'.repeat(50));
            results[suiteName] = {};

            for (const [testName, testFn] of Object.entries(tests)) {
                try {
                    const result = await Promise.race([
                        testFn(token),
                        new Promise((_, reject) =>
                            setTimeout(() => reject(new Error('Test timeout')), TEST_TIMEOUT)
                        )
                    ]);
                    results[suiteName][testName] = result.ok ? 'PASS' : 'FAIL';
                } catch (error) {
                    console.log(`  ❌ Error: ${error.message}`);
                    results[suiteName][testName] = 'ERROR';
                }
            }
        }

        // Summary
        console.log('\n\n' + '='.repeat(50));
        console.log('📊 TEST SUMMARY');
        console.log('='.repeat(50));

        for (const [suiteName, tests] of Object.entries(results)) {
            const passed = Object.values(tests).filter(r => r === 'PASS').length;
            const failed = Object.values(tests).filter(r => r === 'FAIL').length;
            const errors = Object.values(tests).filter(r => r === 'ERROR').length;
            const total = Object.values(tests).length;

            console.log(`${suiteName}: ${passed}/${total} passed`);
            if (failed > 0) console.log(`  ❌ Failed: ${failed}`);
            if (errors > 0) console.log(`  ⚠️ Errors: ${errors}`);
        }

        console.log('='.repeat(50) + '\n');
    } catch (error) {
        console.error('❌ Test suite error:', error);
    }
}

// Export for use in Node.js or browser
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { testSuites, runAllTests, apiCall };
}
