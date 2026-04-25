import { PrismaClient } from '@prisma/client';

let prismaInstance: PrismaClient;

export function getPrisma(): PrismaClient {
  if (!prismaInstance) {
    prismaInstance = new PrismaClient();

    prismaInstance.$connect().then(() => {
      console.log('✅ Database connected successfully');
    }).catch((err) => {
      console.error('❌ Failed to connect to database:', err);
      process.exit(1);
    });
  }

  return prismaInstance;
}

export async function disconnectPrisma(): Promise<void> {
  if (prismaInstance) {
    await prismaInstance.$disconnect();
  }
}

export const db = getPrisma();
