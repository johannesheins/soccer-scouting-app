import {useUser} from "@/hooks/use-auth";

export function useHasRight(rightId: string): boolean {
    const user = useUser();

    if(user.is_administrator){
        return true;
    }
    return user.rights.includes(rightId);
}
