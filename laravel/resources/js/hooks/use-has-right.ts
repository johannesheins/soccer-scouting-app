import {useUser} from "@/hooks/use-auth";

export function useHasRight(rightId: string): boolean {
    return useUser().rights.includes(rightId);
}
