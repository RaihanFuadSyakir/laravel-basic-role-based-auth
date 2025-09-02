import * as z from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
export const formUpdateSchema = toTypedSchema(
  z
    .object({
      id: z.number(),
      name: z
        .string()
        .min(2, { message: 'Name must be at least 2 characters' })
        .max(50, { message: 'Name must be less than 50 characters' }),
      email: z
        .string()
        .email({ message: 'Please enter a valid email address' }),
      change_password: z.boolean().default(false),
      password: z.string().optional(),
      confirmPassword: z.string().optional(),
      roles : z
        .array(z.string())
        .min(1,{message : 'Atleast need 1 role assigned'})
    })
    .refine(
      (data) => {
        if (data.change_password) {
          return !!data.password && data.password.length >= 8;
        }
        return true;
      },
      {
        path: ['password'],
        message: 'Password must be at least 8 characters',
      }
    )
    .refine(
      (data) => {
        if (data.change_password) {
          return data.password === data.confirmPassword;
        }
        return true;
      },
      {
        path: ['confirmPassword'],
        message: 'Passwords do not match',
      }
    )
);
export const formCreateSchema = toTypedSchema(
  z
    .object({
      name: z
        .string()
        .min(2, { message: 'Name must be at least 2 characters' })
        .max(50, { message: 'Name must be less than 50 characters' }),
      email: z
        .string()
        .email({ message: 'Please enter a valid email address' }),
      password: z
        .string()
        .min(8, { message: 'Password must be at least 8 characters' })
        .max(100, { message: 'Password must be less than 100 characters' }),
      confirmPassword: z.string(),
      roles : z
        .array(z.string())
        .min(1,{message : 'Atleast need 1 role assigned'})
    })
    .refine((data) => data.password === data.confirmPassword, {
      path: ['confirmPassword'],
      message: 'Passwords do not match',
    })
);
