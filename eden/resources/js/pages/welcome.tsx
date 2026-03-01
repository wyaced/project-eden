import Impact from '@/components/eden-components/impact';
import Hero from '@/components/eden-components/hero';
import ProblemSection from '@/components/eden-components/problem-section';
import SolutionSection from '@/components/eden-components/solution-section';
import CallToAction from '@/components/eden-components/call-to-action';
import Footer from '@/components/eden-components/footer';

export default function Welcome() {
    return (
        <>
            <Hero />
            <ProblemSection />
            <SolutionSection />
            <Impact />
            <CallToAction />
            <Footer />
        </>
    );
}
