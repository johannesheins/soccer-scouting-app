import {FieldLabel} from "@/components/ui/field";
import {Input} from "@/components/ui/input";
import InputError from "@/components/input-error";

type Props = {
    name: string,
    label?: string,
    value: string,
    setData: (key: string, value: string) => void,
    error?: string,
}

export default function HeightInput({name, label, value, setData, error}: Props){
    return (
        <>
            <FieldLabel htmlFor={name}>{label ?? 'Größe'}</FieldLabel>
            <Input id={name}
                   value={value}
                   onChange={e => setData(name, e.target.value)}
                   placeholder="Größe eintragen"
                   type="number"
            />
            <InputError message={error} />
        </>
    );
}
