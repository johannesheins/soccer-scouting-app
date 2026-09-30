import UserGroupForm from "@/components/from/user-group-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import userGroup from "@/routes/administration/user-group";

export default function UserGroupEdit() {
    return <UserGroupForm edit backHref={userGroup.index.url()}/>;
}

UserGroupEdit.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('User groups'),
            href: userGroup.index(),
        },
        {
            title: t('Edit user group')
        }
    ],
});
