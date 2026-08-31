import React from 'react';
import {
  Code,
  LayoutTemplate,
  ShieldCheck,
  Zap,
  Scale,
  Receipt,
  Briefcase,
  Layers,
  BarChart2,
  Sparkles,
  Camera,
  Building2,
  Heart,
  Scissors,
  Bot,
  FormInput,
  Users2,
  HardDrive,
  Calendar,
  Film,
  Lock,
  Send,
  Bell,
  Terminal,
  Star,
  BrainCircuit,
  Kanban
} from 'lucide-react';

interface IndustryIconProps {
  name: string;
  className?: string;
}

export function IndustryIcon({ name, className = 'w-5 h-5' }: IndustryIconProps) {
  switch (name) {
    case 'Code':
      return <Code className={className} />;
    case 'LayoutTemplate':
      return <LayoutTemplate className={className} />;
    case 'ShieldCheck':
      return <ShieldCheck className={className} />;
    case 'Zap':
      return <Zap className={className} />;
    case 'Scale':
      return <Scale className={className} />;
    case 'Receipt':
      return <Receipt className={className} />;
    case 'Briefcase':
      return <Briefcase className={className} />;
    case 'Layers':
      return <Layers className={className} />;
    case 'BarChart2':
      return <BarChart2 className={className} />;
    case 'Sparkles':
      return <Sparkles className={className} />;
    case 'Camera':
      return <Camera className={className} />;
    case 'Building2':
      return <Building2 className={className} />;
    case 'Heart':
      return <Heart className={className} />;
    case 'Scissors':
      return <Scissors className={className} />;
    case 'Bot':
      return <Bot className={className} />;
    case 'FormInput':
      return <FormInput className={className} />;
    case 'Users2':
      return <Users2 className={className} />;
    case 'HardDrive':
      return <HardDrive className={className} />;
    case 'Calendar':
      return <Calendar className={className} />;
    case 'Film':
      return <Film className={className} />;
    case 'Lock':
      return <Lock className={className} />;
    case 'Send':
      return <Send className={className} />;
    case 'Bell':
      return <Bell className={className} />;
    case 'Terminal':
      return <Terminal className={className} />;
    case 'Star':
      return <Star className={className} />;
    case 'BrainCircuit':
      return <BrainCircuit className={className} />;
    case 'Kanban':
      return <Kanban className={className} />;
    default:
      return <Briefcase className={className} />;
  }
}
