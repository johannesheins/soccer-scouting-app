import MultipleSelector from "@/components/ui/multi-select";
import React from "react";

type Props = React.ComponentProps<typeof MultipleSelector>;

export const SingleSelector = ({ onChange, ...props }: Props) => {
    return (
        <MultipleSelector
            hideClearAllButton={true}
            {...props}
            onChange={opts => onChange?.(opts.slice(-1))}
        />
    );
};
