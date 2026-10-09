import UserForm from "@/components/from/user-form";
import { t } from '@/locale/translate';
import {administration} from '@/routes';
import user from "@/routes/administration/user";

export default function UserCreate() {
    return <UserForm />;
}

UserCreate.layout = () => ({
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
            title: t('Create user')
        }
    ],
});
