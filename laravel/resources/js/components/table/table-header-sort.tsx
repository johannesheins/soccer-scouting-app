import {ArrowUpDown, ArrowUp, ArrowDown} from "lucide-react";
import {Button} from "@/components/ui/button";

// label is a getter so the header is translated at render time, not at module load
export default function sortHeader(label: () => string) {
    return ({column}: { column: any }) => {
        const sorted = column.getIsSorted()
        const Icon = sorted === "asc" ? ArrowUp : sorted === "desc" ? ArrowDown : ArrowUpDown

        return (
            <Button variant="ghost" onClick={() => column.toggleSorting(sorted === "asc")}>
                {label()}
                <Icon className="ml-2 h-4 w-4"/>
            </Button>
        )
    }
}
