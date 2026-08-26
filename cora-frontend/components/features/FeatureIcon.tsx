import React from 'react';
import { 
  Bot, 
  Kanban, 
  LayoutTemplate, 
  Sparkles, 
  FileText, 
  Receipt, 
  Camera, 
  Send, 
  Calendar, 
  FormInput, 
  Star, 
  HardDrive, 
  CheckSquare, 
  Mail, 
  BrainCircuit, 
  Users2, 
  Smartphone, 
  BookOpen, 
  Settings, 
  Compass, 
  Zap, 
  MessageCircle, 
  Image as ImageIcon, 
  CreditCard, 
  Video, 
  GitBranch, 
  PhoneCall, 
  FileSpreadsheet, 
  TabletSmartphone,
  Layers
} from 'lucide-react';

const ICON_MAP: Record<string, React.ElementType> = {
  Bot,
  Kanban,
  LayoutTemplate,
  Sparkles,
  FileText,
  Receipt,
  Camera,
  Send,
  Calendar,
  FormInput,
  Star,
  HardDrive,
  CheckSquare,
  Mail,
  BrainCircuit,
  Users2,
  Smartphone,
  BookOpen,
  Settings,
  Compass,
  Zap,
  MessageCircle,
  ImageIcon,
  CreditCard,
  Video,
  GitBranch,
  PhoneCall,
  FileSpreadsheet,
  TabletSmartphone
};

interface FeatureIconProps {
  name: string;
  className?: string;
}

export function FeatureIcon({ name, className = 'w-5 h-5' }: FeatureIconProps) {
  const IconComponent = ICON_MAP[name] || Layers;
  return <IconComponent className={className} />;
}
