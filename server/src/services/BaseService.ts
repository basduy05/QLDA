// Service base class for business logic
export class BaseService {
  protected handleError(error: any): never {
    console.error('Service error:', error);
    throw error;
  }
}
