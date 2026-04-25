#!/usr/bin/env node

/**
 * Database Seed Script
 * Populates the database with initial data for development
 */

const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Starting database seed...');

  // Clean existing data (dev only)
  console.log('Cleaning existing data...');
  await prisma.projectMember.deleteMany({});
  await prisma.chatGroupMember.deleteMany({});
  await prisma.chatMessage.deleteMany({});
  await prisma.chatGroup.deleteMany({});
  await prisma.directMessage.deleteMany({});
  await prisma.directConversation.deleteMany({});
  await prisma.taskComment.deleteMany({});
  await prisma.taskAttachment.deleteMany({});
  await prisma.subtask.deleteMany({});
  await prisma.callSession.deleteMany({});
  await prisma.notification.deleteMany({});
  await prisma.aiMessage.deleteMany({});
  await prisma.task.deleteMany({});
  await prisma.project.deleteMany({});
  await prisma.user.deleteMany({});

  // Create demo users
  console.log('Creating demo users...');
  const hashedPassword = await bcrypt.hash('password123', 10);

  const user1 = await prisma.user.create({
    data: {
      email: 'admin@aperlex.com',
      name: 'Admin User',
      password: hashedPassword,
      role: 'ADMIN',
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=admin',
      isOnline: true,
    },
  });

  const user2 = await prisma.user.create({
    data: {
      email: 'john@example.com',
      name: 'John Doe',
      password: hashedPassword,
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=john',
      isOnline: true,
    },
  });

  const user3 = await prisma.user.create({
    data: {
      email: 'jane@example.com',
      name: 'Jane Smith',
      password: hashedPassword,
      avatar: 'https://api.dicebear.com/7.x/avataaars/svg?seed=jane',
      isOnline: false,
    },
  });

  console.log('✓ Created 3 users');

  // Create demo project
  console.log('Creating demo project...');
  const project = await prisma.project.create({
    data: {
      name: 'Website Redesign',
      description: 'Complete redesign of the company website',
      ownerId: user1.id,
      status: 'active',
      startDate: new Date('2024-01-01'),
      endDate: new Date('2024-06-30'),
      members: {
        create: [
          { userId: user1.id, role: 'LEAD' },
          { userId: user2.id, role: 'MEMBER' },
          { userId: user3.id, role: 'MEMBER' },
        ],
      },
    },
  });

  console.log('✓ Created project: ' + project.name);

  // Create demo tasks
  console.log('Creating demo tasks...');
  const task1 = await prisma.task.create({
    data: {
      projectId: project.id,
      title: 'Design homepage mockup',
      description: 'Create Figma mockup for the new homepage',
      status: 'IN_PROGRESS',
      priority: 'HIGH',
      dueDate: new Date('2024-02-15'),
      assignedTo: user2.id,
      subtasks: {
        create: [
          { title: 'Create wireframe' },
          { title: 'Design mockup' },
          { title: 'Get client feedback' },
        ],
      },
    },
  });

  const task2 = await prisma.task.create({
    data: {
      projectId: project.id,
      title: 'Setup development environment',
      description: 'Install all necessary dependencies and tools',
      status: 'DONE',
      priority: 'URGENT',
      dueDate: new Date('2024-01-15'),
      assignedTo: user3.id,
    },
  });

  const task3 = await prisma.task.create({
    data: {
      projectId: project.id,
      title: 'Frontend development',
      description: 'Build responsive UI components',
      status: 'TODO',
      priority: 'HIGH',
      dueDate: new Date('2024-03-31'),
      assignedTo: user1.id,
    },
  });

  console.log('✓ Created 3 tasks with subtasks');

  // Create demo chat group
  console.log('Creating demo chat group...');
  const chatGroup = await prisma.chatGroup.create({
    data: {
      name: 'Web Team',
      description: 'Team chat for website redesign project',
      avatar: 'https://api.dicebear.com/7.x/bottts/svg?seed=webteam',
      members: {
        create: [
          { userId: user1.id },
          { userId: user2.id },
          { userId: user3.id },
        ],
      },
    },
  });

  console.log('✓ Created chat group');

  // Create demo messages
  console.log('Creating demo messages...');
  await prisma.chatMessage.createMany({
    data: [
      {
        groupId: chatGroup.id,
        senderId: user1.id,
        content: 'Welcome to the Web Team! Let\'s discuss the project.',
      },
      {
        groupId: chatGroup.id,
        senderId: user2.id,
        content: 'Thanks! I\'ve started working on the mockup.',
      },
      {
        groupId: chatGroup.id,
        senderId: user3.id,
        content: 'Sounds great! I\'ll begin with the frontend setup.',
      },
    ],
  });

  console.log('✓ Created chat messages');

  console.log('✅ Database seed completed successfully!');
  console.log('\n📝 Test Account:');
  console.log('  Email: admin@aperlex.com');
  console.log('  Password: password123');
}

main()
  .then(async () => {
    await prisma.$disconnect();
  })
  .catch(async (e) => {
    console.error('❌ Seed error:', e);
    await prisma.$disconnect();
    process.exit(1);
  });
