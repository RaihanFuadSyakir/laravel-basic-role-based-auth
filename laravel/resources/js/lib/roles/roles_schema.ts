
import * as z from 'zod';
export const formUpdateSchema = z.object({
  id: z.number().min(1, "ID must be greater than 0"),
  name: z.string().min(1, "Name is required"),
  permissions: z.array(z.string()).min(1, "At least one permission is required"),
  level : z.array(
            z.number().min(1,"Highest Level 1").max(10,"Lowest Level 10"))
            .length(1,"Only 1 level required"),
});

export const formCreateSchema = z.object({
  name: z.string().min(1, "Name is required"),
  permissions: z.array(z.string()).min(1, "At least one permission is required"),
  level : z.array(
            z.number().min(1,"Highest Level 1").max(10,"Lowest Level 10"))
            .length(1,"Only 1 level required"),
});
