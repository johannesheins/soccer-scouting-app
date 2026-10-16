import {router} from "@inertiajs/react";
import {HistoryDialog} from "@/components/history-dialog";
import { t } from '@/locale/translate';
import {UserGroupView} from "@/pages/administration/user-group/user-group-view";
import userGroupRoute from "@/routes/administration/user-group";
import type {RightGroup} from "@/types/right";
import type {UserGroup, UserGroupVersion} from "@/types/user-group";

type Props = {
    userGroup: UserGroup,
    versions: UserGroupVersion[],
    rightGroups: RightGroup[],
}
export default function UserGroupViewHistory({userGroup, versions, rightGroups}: Props) {
    const fields: Record<string, string> = {
        name: t('Group name'),
        rights: t('Rights'),
    };

    const restoreVersion = (version: UserGroupVersion) => {
        router.put(userGroupRoute.update.url(userGroup.id), version.new_values);
    };

    return (
        <HistoryDialog variant="large" versions={versions} fields={fields} onRestore={restoreVersion}>
            {version => <UserGroupView userGroup={version.userGroup} rightGroups={rightGroups}/>}
        </HistoryDialog>
    );
}
