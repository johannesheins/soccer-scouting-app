import {useUser} from "@/hooks/use-auth";

export function useHasRight(rightId: number): boolean {
    const user = useUser();

    if(user.is_administrator){
        return true;
    }
    return user.rights.includes(rightId);
}
