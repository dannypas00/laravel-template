export enum JobStatusEnum {
  'QUEUED' = 'queued',
  'RUNNING' = 'running',
  'SUCCEEDED' = 'succeeded',
  'FAILED' = 'failed',
}
export type UserData = {
  id: number;
  name: string;
  email: string;
  profile_photo_url: string;
  email_verified_at: string | null;
  created_at: string | null;
  updated_at: string | null;
};
