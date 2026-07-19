import {format as f, Locale} from "date-fns";
import {de} from "date-fns/locale";

export function date(date: string, format: string = "dd.MM.yyyy", locale: Locale = de): string {
    return f(new Date(date), format, {locale: locale})
}
