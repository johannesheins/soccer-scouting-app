import UserGroupForm from "@/components/from/user-group-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import userGroup from "@/routes/administration/user-group";

export default function UserGroupCreate() {
    return <UserGroupForm />;
}

UserGroupCreate.layout = () => ({
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
            title: t('Create user group')
        }
    ],
});
