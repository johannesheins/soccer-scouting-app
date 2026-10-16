import {Badge} from "@/components/ui/badge";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Separator} from "@/components/ui/separator";
import { t } from '@/locale/translate';
import type {RightGroup} from "@/types/right";
import type {UserGroupVersion} from "@/types/user-group";

type Props = {
    userGroup: UserGroupVersion['userGroup'],
    rightGroups: RightGroup[],
}
export function UserGroupView({userGroup, rightGroups}: Props) {
    const rightIds = userGroup.rights.map(right => right.id);
    const grantedGroups = rightGroups
        .map(group => ({...group, rights: group.rights.filter(right => rightIds.includes(right.id))}))
        .filter(group => group.rights.length > 0);

    return (
        <Card className="border-none shadow-none py-1 w-full">
            <CardHeader className="pb-2">
                <CardTitle><p className="text-2xl">{userGroup.name}</p></CardTitle>
            </CardHeader>

            <Separator/>

            <CardContent className="mt-1 grid gap-4">
                {grantedGroups.length <= 0 && <p className="text-muted-foreground">{t('No rights assigned')}</p>}
                {grantedGroups.map(group => (
                    <div key={group.id}>
                        <p className="text-muted-foreground text-xs uppercase tracking-wide mb-2">{group.name}</p>
                        <div className="flex flex-wrap gap-1">
                            {group.rights.map(right => (
                                <Badge key={right.id} variant="secondary" title={right.description}>{right.name}</Badge>
                            ))}
                        </div>
                    </div>
                ))}
            </CardContent>
        </Card>
    );
}
