import UserForm from "@/components/from/user-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import user from "@/routes/administration/user";

export default function UserEdit() {
    return <UserForm edit backHref={user.index.url()}/>;
}

UserEdit.layout = () => ({
    breadcrumbs: [
        {
            title: t('Administration'),
            href: administration(),
        },
        {
            title: t('Users'),
            href: user.index(),
        },
        {
            title: t('Edit user')
        }
    ],
});
